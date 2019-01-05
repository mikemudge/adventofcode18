<?php
$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

$first = explode(" ", $lines[0])[2];
$initialState = "......" . $first. ".....";
// Keep track of what index the state starts at.
$offset = -6;
array_shift($lines);
array_shift($lines);

foreach($lines as $line) {
  $parts = explode(" ", $line);
  $stateMap[$parts[0]] = $parts[2];
}

print_r($stateMap);
echo('state = ' . $initialState . "\n");
$previousState = str_split($initialState);

for ($g=0; $g < 300; $g++) {
  $total = count($previousState);
  if ($g % 1000 == 0) {
    echo("$g / $generations complete\n");
    echo("$offset state = " . join($previousState) . "\n");
    echo($total . "\n");
  }
  $state = ['.','.'];
  for ($i=0; $i < $total - 4; $i++) {
    $lastState = $previousState[$i] . $previousState[$i + 1]  . $previousState[$i + 2]  . $previousState[$i + 3]  . $previousState[$i + 4];
    $state[] = $stateMap[$lastState];
  }
  $state[] = '.';
  $state[] = '.';
  if ($g <= 20) {
    $previousStage = join("", $state);
    echo("$offset state = " . $previousStage . "\n");
  }

  // TODO trim down as well?
  // Add an extra dot for growth.
  $state[] = '.';
  $firstPlant = 6;
  for ($i=0; $i < count($state); $i++) {
    if ($state[$i] != '.') {
      $firstPlant = $i;
      break;
    }
  }
  $indexOfFirst = $firstPlant + $offset;
  if ($firstPlant >= 106) {
    // Only splice when we can remove a good number of items.
    $offset += $firstPlant - 6;
    array_splice($state, 0, $firstPlant - 6);
    echo("$indexOfFirst @ time $g\n");
    echo("$offset state = " . join($state) . "\n");
  }
  if ($g > 200) {
    echo("$indexOfFirst @ time $g\n");
    echo("$offset state = " . join($state) . "\n");
  }
  $previousState = $state;

  if ($g <= 20) {
    echo("$offset padded= " . join($state) . "\n");
  }
  if ($g == 19) {
    $answer = 0;
    for ($i=0; $i < count($state); $i++) {
      if ($state[$i] == '#') {
        $answer += $i + $offset;
      }
    }

    echo("Part 1: " . $answer . "\n");
  }
}

// Manually detected the pattern, which is repeated each generation with an offset of +1.
$commonPattern = "#....#.................................................#.....................................#.......#";
$state = str_split($commonPattern);
$generations = 50000000000 - 1;
$offset = $generations - 6;
$answer = 0;
for ($i=0; $i < count($state); $i++) {
  if ($state[$i] == '#') {
    $answer += $i + $offset;
  }
}
// 250000000224 is too high.
echo("Part 2: " . $answer . "\n");
