<?php

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

$nanoBots = [];
$maxRange;
$maxRangeNanoBot;
foreach($lines as $line) {
  echo($line. "\n");
  $parts = explode(",", substr($line, 5));
  $x = intval($parts[0]);
  $y = intval($parts[1]);
  $z = intval(substr($parts[2], 0, -1));

  $r = intval(substr($parts[3], 3));

  $nanoBot = [
    'x' => $x,
    'y' => $y,
    'z' => $z,
    'r' => $r
  ];
  $nanoBots[] = $nanoBot;

  if ($r > $maxRange) {
    $maxRange = $r;
    $maxRangeNanoBot = $nanoBot;
  }
  if ($line != "pos=<$x,$y,$z>, r=$r") {
    echo("pos=<$x,$y,$z>, r=$r\n");
  }
}

$numNanobots = 0;
foreach($nanoBots as $nanoBot) {
  $dis = abs($maxRangeNanoBot['x'] - $nanoBot['x']);
  $dis += abs($maxRangeNanoBot['y'] - $nanoBot['y']);
  $dis += abs($maxRangeNanoBot['z'] - $nanoBot['z']);
  if ($dis <= $maxRange) {
    $numNanobots++;
  }
}
echo("Part 1: $numNanobots\n");

// TODO figure out how to find the location with most nanobots in range?
// There are only 1000 nanobots. But the number of coordinates is very large.
// Use some kind of area mapping to locate positions?

// echo("Part 2: " . . "\n");
