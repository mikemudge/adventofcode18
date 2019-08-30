<?php

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$data = file_get_contents($filename);
$line = explode("\n", $data)[0];

function calculateOptions(&$letters, &$index) {
  $options = [];
  $letter = null;
  while($letter != ')') {
    // Move foward to the start of the next option
    $index++;
    // Parse a path and add it to the options.
    $currentOption = calculatePath($letters, $index);
    echo("Adding option " . $currentOption[0] . "\n");
    echo("index at $index, letter = $letter\n");
    $options[] = $currentOption;
    $letter = $letters[$index];
  }
  return [
    'branches' => $options
  ];
}

function calculatePath(&$letters, &$index) {
  $currentPath = [];
  $result = [];

  for (;$index < count($letters); $index++) {
    $letter = $letters[$index];
    if ($letter == '(') {
      // Add the prefix into the result.
      if (!empty($currentPath)) {
        $result[] = implode("", $currentPath);
      }
      $currentPath = [];

      // Recursively calculate paths.
      echo("Entering subpath at index $index\n");
      $nestedPaths = calculateOptions($letters, $index);
      // Add the options to the result.
      if (!empty($nestedPaths)) {
        $result[] = $nestedPaths;
      }
      echo("Completed subpath at index $index\n");
      continue;
    } elseif ($letter == '|' || $letter == ')') {
      // This is the end of this set of paths.
      // Add the currentPath and return the result.
      if ($currentPath) {
        $result[] = implode("", $currentPath);
      }
      return $result;
    } else {
      // normal direction letters.
      $currentPath[] = $letter;
    }
  }
  // Reached the end of letters.
  if (!empty($currentPath)) {
    $result[] = implode("", $currentPath);
  }
  return $result;
}

function printPath($paths) {
  if (array_key_exists('branches', $paths)) {
    echo("(");
    $first = true;
    foreach($paths['branches'] as $b) {
      if (!$first) {
        echo("|");
      }
      printPath($b);
      $first = false;
    }
    echo(")");
    // TODO can the array have other keys?
    return;
  }
  foreach($paths as $p) {
    if (is_array($p)) {
      printPath($p);
    } else {
      echo($p);
    }
  }
}

function followPath($paths, &$grid, $x, $y) {
  if (array_key_exists('branches', $paths)) {
    // Handle branches?
    foreach($paths['branches'] as $b) {
      followPath($b, $grid, $x, $y);
    }
  } else {
    foreach($paths as $p) {
      if (is_array($p)) {
        followPath($p, $grid, $x, $y);
      } else {
        // walk the path and update $x, $y;
        $letters = str_split($p);
        foreach($letters as $l) {
          $dx = 0;
          $dy = 0;
          if ($l == 'N') {
            $dy=-1;
          } elseif($l == 'S') {
            $dy=+1;
          } elseif($l == 'W') {
            $dx=-1;
          } elseif($l == 'E') {
            $dx=+1;
          } else {
            throw new Exception("Error Processing letter $l");
          }
          $door = "|";
          if ($dy != 0) {
            $door = "-";
          }
          $grid[$y+$dy][$x+$dx] = $door;
          $x += $dx * 2;
          $y += $dy * 2;
          $grid[$y][$x] = ".";
        }
      }
    }
  }
}

function printGrid($grid) {
  for($y=45;$y<56;$y++) {
    if (isset($grid[$y])) {
      for($x=0;$x<100;$x++) {
        if (isset($grid[$y][$x])) {
          echo($grid[$y][$x]);
        } else {
          echo("#");
        }
      }
    } else {
      echo(str_repeat("#", 100));
    }
    echo("\n");
  }
}

function calculateDistances($grid, &$distances, $x, $y, $dis) {
  if (isset($distances[$y][$x]) && $dis >= $distances[$y][$x]) {
    // Point already reached in less steps.
    return;
  }
  // State that we can reach this in $dis steps.
  $distances[$y][$x] = $dis;

  // And go through all the doors in a depth first manner.
  // Could be made more efficient, but this uses less memory.
  if (isset($grid[$y-1][$x]) && $grid[$y-1][$x] == '-') {
    calculateDistances($grid, $distances, $x, $y-2, $dis + 1);
  }
  if (isset($grid[$y+1][$x]) && $grid[$y+1][$x] == '-') {
    calculateDistances($grid, $distances, $x, $y+2, $dis + 1);
  }
  if (isset($grid[$y][$x-1]) && $grid[$y][$x-1] == '|') {
    calculateDistances($grid, $distances, $x-2, $y, $dis + 1);
  }
  if (isset($grid[$y][$x+1]) && $grid[$y][$x+1] == '|') {
    calculateDistances($grid, $distances, $x+2, $y, $dis + 1);
  }
}

// Sample
// $line = "^ENWWW(NEEE|SSE(EE|N))$";
// Sample 2
// $line = "^ENNWSWW(NEWS|)SSSEEN(WNSE|)EE(SWEN|)NNN$";
$currentPath = [];

// Remove ^ and $
$line = substr($line, 1, -1);
$letters = str_split($line);
$index = 0;
$paths = calculatePath($letters, $index);

echo($line . "\n");
printPath($paths);
echo("\n");

$grid = [];
$grid[50] = [];
$grid[50][50] = "X";

followPath($paths, $grid, 50, 50);

printGrid($grid);

$distances = [];
$distances[50] = [];

calculateDistances($grid, $distances, 50, 50, 0);
echo("\n");

printGrid($distances);

$biggest = 0;
foreach($distances as $row) {
  foreach($row as $value) {
    $biggest = max($biggest, $value);
  }
}

echo("Part 1: $biggest\n");

$rooms = 0;
foreach($distances as $row) {
  foreach($row as $value) {
    if ($value >= 1000) {
      $rooms++;
    }
  }
}

echo("Part 2: $rooms\n");
