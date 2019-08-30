<?php
$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
// $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample";
// $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample2";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

function printGrid($grid) {
  for($y=0;$y<count($grid);$y++) {
    for($x=0;$x<count($grid[$y]);$x++) {
      if (isset($grid[$y][$x])) {
        echo($grid[$y][$x]);
      } else {
        echo("?");
      }
    }
    echo("\n");
  }
}

function getNearbyCounts($x, $y, $grid) {
  $has = [
    '.' => 0,
    '|' => 0,
    '#' => 0
  ];
  for ($dy=-1;$dy<=1;$dy++) {
    for ($dx=-1;$dx<=1;$dx++) {
      if ($dx == 0 && $dy == 0) {
        continue;
      }
      $ty = $y + $dy;
      $tx = $x + $dx;
      if (isset($grid[$ty][$tx])) {
        $has[$grid[$ty][$tx]]++;
      }
    }
  }
  return $has;
}

function iterate($grid) {
  $nextGrid = [];
  for($y=0;$y<count($grid);$y++) {
    for($x=0;$x<count($grid[$y]);$x++) {
      $has = getNearbyCounts($x, $y, $grid);
      switch($grid[$y][$x]) {
        case '.':
          // open
          if ($has['|'] >= 3) {
            $nextGrid[$y][$x] = '|';
          } else {
            $nextGrid[$y][$x] = '.';
          }
          break;
        case '|':
          // Tree
          if ($has['#'] >= 3) {
            $nextGrid[$y][$x] = '#';
          } else {
            $nextGrid[$y][$x] = '|';
          }
          break;
        case '#':
          // Lumberyard
          if ($has['#'] >= 1 && $has['|'] >= 1) {
            $nextGrid[$y][$x] = '#';
          } else {
            $nextGrid[$y][$x] = '.';
          }
          break;
      }
    }
  }

  return $nextGrid;
}

foreach ($lines as $y=>$line) {
  $grid[$y] = str_split($line);
}

printGrid($grid);

for($i=0;$i<10;$i++) {
  echo("Iteration $i:\n");
  $grid = iterate($grid);
  printGrid($grid);
}

$counters = [
    '.' => 0,
    '|' => 0,
    '#' => 0
];
for($y=0;$y<count($grid);$y++) {
  for($x=0;$x<count($grid[$y]);$x++) {
    $counters[$grid[$y][$x]]++;
  }
}
print_r($counters);
$answer = $counters['|'] * $counters['#'];
echo("Part 1: $answer\n");

// TODO find a pattern of grids which we can use to predict far into the future.
// $grid = iterate($grid);

// We want to ensure we reach the recurring pattern first, so iterate 1000 times.
for($i=0;$i<1000;$i++) {
  $grid = iterate($grid);
}

// Now we need to find a value which will match 1000000000.
// Pattern recurs every 196 iterations so we can just use the modulus.
// 1010 is the number of iterations already completed.
$numberCycles = intval((1000000000 - 1010) % 196);

for($i=0;$i<$numberCycles;$i++) {
  $grid = iterate($grid);
}
echo("Iteration " . (1010 + $numberCycles) . ":\n");
printGrid($grid);

$counters = [
    '.' => 0,
    '|' => 0,
    '#' => 0
];
for($y=0;$y<count($grid);$y++) {
  for($x=0;$x<count($grid[$y]);$x++) {
    $counters[$grid[$y][$x]]++;
  }
}
print_r($counters);
$answer = $counters['|'] * $counters['#'];
echo("Part 2: $answer\n");
