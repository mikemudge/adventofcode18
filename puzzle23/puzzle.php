<?php

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$data = file_get_contents($filename);

$nanoBots = [];
for ($data as $line) {
  echo($line);

}
$numNanobots = 0;

echo("Part 1: $numNanobots\n");

// echo("Part 2: " . . "\n");
