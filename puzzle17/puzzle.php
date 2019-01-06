<?php
$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
// $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample";
// $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample2";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

function printGrid($grid, $sx) {
  for($y=$sx;$y<$sx + 500;$y++) {
    for($x=400;$x<570;$x++) {
      if ($x === 500 && $y === 0) {
        echo("+");
        continue;
      }

      if (isset($grid[$y][$x])) {
        echo($grid[$y][$x]);
      } else {
        echo(".");
      }
    }
    echo("\n");
  }
}

$minY = 500;
$maxDepth = 0;
$maxX = 0;
$minX = 500;
foreach ($lines as $line) {
  $parts = explode(",", $line);
  $first = explode("=", $parts[0]);
  $axis = $first[0];
  $value = intval($first[1]);
  $range = explode("..", explode("=", $parts[1])[1]);
  $rangeArr = range($range[0], $range[1]);
  if ($axis === 'x') {
    foreach($rangeArr as $y) {
      $grid[$y][$value] = '#';
    }
    $maxX = max($maxX, $value);
    $minX = min($minX, $value);
    $minY = min($minY, $range[0]);
    $maxDepth = max($maxDepth, $range[1]);
  } else {
    foreach($rangeArr as $x) {
      $grid[$value][$x] = '#';
    }
    $maxX = max($maxX, $range[1]);
    $minX = min($minX, $range[0]);
    $minY = min($minY, $value);
    $maxDepth = max($maxDepth, $value);
  }
}
// Calculate water cover.

// Make sure these completely cover the area.
$maxX++;
$minX--;

$x = 500;
$fallPoints = [[
  'x' => $x,
  'y' => $minY
]];

$waterLocations = 0;
$permWater = 0;
for($i=0;$i<5000;$i++) {
  if (empty($fallPoints)) {
    break;
  }
  $p = array_shift($fallPoints);
  echo("Turn $i - fallPoints = " . count($fallPoints) . " y=" . $p['y'] . "\n");
  $starty = $p['y'];
  $x = $p['x'];
  $fillContainer = false;
  for ($y=$starty; $y<=$maxDepth; $y++) {
    if (isset($grid[$y][$x])) {
      echo("Bottomed at $x,$y on " . $grid[$y][$x] . "\n");
      if ($grid[$y][$x] === '#' || $grid[$y][$x] === '~') {
        $fillContainer = true;
      }
      break;
    } else {
      $grid[$y][$x] = "|";
      $waterLocations++;
    }
  }
  if (!$fillContainer) {
    // Only fill containers if a # or ~ was landed on.
    echo("Not filling containers\n");
    continue;
  }

  // Found a bottom at $x, $y
  // Calculate width and if the water will be static.
  $fallLeft = null;
  $fallRight = null;
  while($fallRight == null && $fallLeft == null) {
    // Move up and fill left and right until fall points are found.
    $y--;
    if (!isset($grid[$y][$x])) {
      // Happens when we fill above the fall point.
      $grid[$y][$x] = "|";
      $waterLocations++;
    }
    for ($lx = $x - 1; $lx>=$minX; $lx--) {
      // Check for a wall.
      if (isset($grid[$y][$lx])) {
        if ($grid[$y][$lx] === '#') {
          // Stop
          break;
        }
      } else {
        $grid[$y][$lx] = "|";
        $waterLocations++;
      }

      // Check underneath
      if (!isset($grid[$y + 1][$lx]) || $grid[$y + 1][$lx] === '|') {
        // Nothing under this, so add a fall point and stop.
        $fallLeft = [
          'x' => $lx,
          'y' => $y + 1
        ];
        break;
      }
    }
    for ($rx = $x + 1; $rx<=$maxX; $rx++) {
      // Check for a wall.
      if (isset($grid[$y][$rx])) {
        if ($grid[$y][$rx] === '#') {
          // Stop
          break;
        }
      } else {
        $grid[$y][$rx] = "|";
        $waterLocations++;
      }

      // Check underneath
      if (!isset($grid[$y + 1][$rx]) || $grid[$y + 1][$rx] === '|') {
        // Nothing under this, so add a fall point and stop.
        $fallRight = [
          'x' => $rx,
          'y' => $y + 1
        ];
        break;
      }
    }

    if ($fallLeft == null && $fallRight == null) {
      for ($sx = $lx + 1; $sx<$rx; $sx++) {
        if (!isset($grid[$y][$sx]) || $grid[$y][$sx] !== '~') {
          $grid[$y][$sx] = '~';
          $permWater++;
        }
      }
    }
  }
  if ($fallRight != null) {
    $fallPoints[] = $fallRight;
  }
  if ($fallLeft != null) {
    $fallPoints[] = $fallLeft;
  }
}


// Add fall locations.
printGrid($grid, 0);
echo("depth = $maxDepth, min = $minY\n");
echo("x = $minX - $maxX\n");
echo("Part 1: $waterLocations\n");
echo("Part 2: $permWater\n");
// 27503 is too high.
// 25419
