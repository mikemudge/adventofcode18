<?php

function create_grid($serial_id) {
  $grid = [];
  for ($y=0;$y<300;$y++) {
    for ($x=0;$x<300;$x++) {
      $rack_id = $x + 10;
      $value = $rack_id * $y;
      $value = $value + $serial_id;
      $value = $value * $rack_id;
      // Find the hundreds digit.
      $hundreds = floor($value / 100) % 10;
      $value = $hundreds;

      $value = $value - 5;
      $grid[$y][$x] = $value;
    }
  }
  return $grid;
}

$cache = [];
function sum_square($x, $y, $size) {
  global $grid, $cache;
  if (isset($cache[$y][$x][$size])) {
    return $cache[$y][$x][$size];
  }
  if ($x + $size > 300 || $y + $size > 300) {
    throw new Exception('Bad Square');
  }

  if ($size > 3) {
    // Use parts?
    // TODO can be more efficient to break up into cached squares?
    // How to deal with odd numbers? Squares aren't possible?
    // 00011
    // 00011
    // 00033
    // 22333
    // 22333
    // Can count the center twice and minus it?
    $half = ceil($size / 2);
    $total = 0;
    // summing 11,282,17
    // 11,282, 9
    $total += sum_square($x, $y, $half);
    $total += sum_square($x + $half, $y, $size - $half);
    $total += sum_square($x, $y + $half, $size - $half);
    if ($size % 2 == 1) {
      $total += sum_square($x - 1 + $half, $y - 1 + $half, $half);
      $total -= $grid[$y + $half][$x + $half];
    } else {
      $total += sum_square($x + $half, $y + $half, $half);
    }
  } else {
    // Actually sum the values.
    $total = 0;
    for ($dy=0;$dy<$size;$dy++) {
      for ($dx=0;$dx<$size;$dx++) {
        $total += $grid[$y + $dy][$x + $dx];
      }
    }
  }
  // Cache all non trivial squares.
  if ($size > 1) {
    $cache[$y][$x][$size] = $total;
  }
  return $total;
}

$grid = create_grid(8);
echo("Test " . $grid[5][3] . " == 4\n");

$grid = create_grid(57);
echo("Test " . $grid[79][122] . " == -5\n");
$grid = create_grid(39);
echo("Test " . $grid[196][217] . " == 0\n");
$grid = create_grid(71);
echo("Test " . $grid[153][101] . " == 4\n");

global $grid;
$grid = create_grid(9110);

$best = -50;
for ($y=0;$y<298;$y++) {
  for ($x=0;$x<298;$x++) {
    $total = sum_square($x, $y, 3);
    if ($total > $best) {
      $best_pt = [$x, $y];
      $best = $total;
    }
  }
}

echo("Part 1: " . $best_pt[0] . "," . $best_pt[1] . " with " . $best . "\n");

$best2 = -50;
for ($size=2;$size<300;$size++) {
  $top = 300 - $size;
  echo("Size $size / 300 completed, $top square options\n");
  for ($y=0;$y<$top;$y++) {
    for ($x=0;$x<$top;$x++) {
      $total = sum_square($x, $y, $size);
      if ($total > $best2) {
        echo("New best $best2\n");
        $best_pt = [$x, $y, $size];
        $best2 = $total;
      }
    }
  }
}

echo("Part 2: " . $best_pt[0] . "," . $best_pt[1] . "," . $best_pt[2] . " with " . $best2 . "\n");
