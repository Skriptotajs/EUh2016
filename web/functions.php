<?php



function score2fontsize($score)
{
    $max = 0.35;
    return intval( 15 + 20 * min($score, $max)/$max );
}

function radius2style($r)
{
	return "width: ".$r."%; padding-bottom:".$r."%; margin-top: -". $r/2 ."%; margin-left: -". $r/2 ."%;";
}

$max_size = null;
$min_size = null;

foreach($groups AS &$g)
{
	if($max_size === null || $max_size < $g['size'])
		$max_size = $g['size'];

	if($min_size === null || $min_size > $g['size'])
		$min_size = $g['size'];
}
unset($g);


foreach($groups AS &$g)
{
	$g['radius'] = 10 + 26 * (($g['size']-$min_size)/($max_size-$min_size));
}
unset($g);


foreach($groups AS &$g)
{
	if(!isset($g['top_phrases']))
		continue;

	$max_size = null;
	$min_size = null;
	$phrases = [];
	for ($i=0; $i<=7; $i++) {
		$p = $g['top_phrases'][$i];
		$w = $p['local'];

		if($max_size === null || $max_size < $w)
			$max_size = $w;

		if($min_size === null || $min_size > $w)
			$min_size = $w;

		$phrases[] = [
			'weight' => $w,
			'token'  => $p['token']
		];
	}

	foreach ($phrases as &$p) {
		$w = $p['weight'];
		$koef = $max_size == $min_size ? 1 : ($w-$min_size)/($max_size-$min_size);
		$p['font_size'] = intval( 20 + 15 * $koef );
	}
	unset($p);

	$g['top_phrases'] = $phrases;
}
unset($g);