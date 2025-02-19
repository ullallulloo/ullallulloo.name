<?php

$data = [];

$country_files = glob(__DIR__."/../../../../\anbennar-eu4-fork-public-build\common\countries/*.txt");
foreach ($country_files as $country_file) {
	$name = basename($country_file, ".txt");
	if (preg_match("/_|\d/", $name))
		continue;
	$country_data = file_get_contents($country_file);
	$color = preg_match("/color\s*=\s*{\s*(\d+\s+\d+\s+\d+)\s*}/", $country_data, $matches);
	$color = explode(" ", $matches[1]);
	$color = array_map(fn($x) => intval($x), $color);
	$data[] = [
		'name' => $name,
		'color' => '#'.sprintf('%02x%02x%02x', $color[0], $color[1], $color[2]),
		'hue' => get_hue(['r' => $color[0], 'g' => $color[1], 'b' => $color[2]]),
	];
}

usort($data, fn($a, $b) => $a['hue'] <=> $b['hue']);

echo '<table class="sortable" cellpadding="5">';
echo '<thead><tr><th>Color</th><th>Name</th></tr></thead><tbody>';
foreach ($data as $row) {
	echo '<tr>';
	echo '<td style="background-color:'.$row['color'].'" data-color="'.$row['color'].'" data-sort="'.$row['hue'].'">&nbsp;</td>';
	echo '<td>'.$row['name'].'</td>';
	echo '</tr>';
}
echo '</tbody></table>';

function get_hue($rgb): float|null {
	$min = min($rgb);
	$chroma = max($rgb) - $min;
	if ($chroma === 0)
		return null;
	if ($rgb['r'] === $min)
		return 60 * (3 - ($rgb['g'] - $rgb['b']) / $chroma);
	if ($rgb['g'] === $min)
		return 60 * (5 - ($rgb['b'] - $rgb['r']) / $chroma);
	return 60 * (1 - ($rgb['r'] - $rgb['g']) / $chroma);
}
