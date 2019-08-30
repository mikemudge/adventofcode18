<?php

$types = [".", "=", "|"];

function printGrid($grid) {
  global $types;
  for($y=0;$y<30;$y++) {
    if (isset($grid[$y])) {
      for($x=0;$x<20;$x++) {
        if (isset($grid[$y][$x])) {
          echo($types[$grid[$y][$x] % 3]);
        } else {
          echo("?");
        }
      }
    } else {
      echo(str_repeat("#", 20) . "\n");
      break;
    }
    echo("\n");
  }
}

function calculateErosionLevel($x, $y, $depth, &$erosionLevels) {
  if (isset($erosionLevels[$y][$x])) {
    return $erosionLevels[$y][$x];
  }
  $geologicIndex = 0;
  if ($y == 0) {
    $geologicIndex = $x * 16807;
  } elseif ($x == 0) {
    $geologicIndex = $y * 48271;
  } else {
    $geologicIndex = calculateErosionLevel($x, $y-1, $depth, $erosionLevels);
    $geologicIndex *= calculateErosionLevel($x-1, $y, $depth, $erosionLevels);
  }

  $result = $geologicIndex + $depth;
  $erosionLevel = $result % 20183;
  $erosionLevels[$y][$x] = $erosionLevel;
  return $erosionLevel;
}

// Example
$depth1 = 510;
$depth = $depth1;
$target_x = 10;
$target_y = 10;
$erosionLevels = [[]];

calculateErosionLevel($target_x, $target_y, $depth1, $erosionLevels);
// Special case for the start and target location.
$erosionLevels[$target_y][$target_x] = $depth1;
$erosionLevels[0][0] = $depth1;
printGrid($erosionLevels);

$risk = 0;
foreach($erosionLevels as $row){
  foreach($row as $value) {
    $risk += $value % 3;
  }
}
echo($risk . "\n");

// Do the real thing.
$depth = 7305;
$target_x = 13;
$target_y = 734;
$erosionLevels = [[]];

calculateErosionLevel($target_x, $target_y, $depth, $erosionLevels);
// Special case for the start and target location.
$erosionLevels[0][0] = $depth;
$erosionLevels[$target_y][$target_x] = $depth;
printGrid($erosionLevels);

$risk = 0;
foreach($erosionLevels as $row){
  foreach($row as $value) {
    $risk += $value % 3;
  }
}

echo("Part 1: $risk\n");

$TORCH = 'i';
$CLIMBING = '&';
$NOTHING = '/';

$pq = new SplPriorityQueue();
$pq->setExtractFlags(SplPriorityQueue::EXTR_BOTH);

$dis = $target_x + $target_y;
$visited = [[[]]];
$pq->insert([
  'x' => 0,
  'y' => 0,
  'tool' => $TORCH,
  'cost' => 0
], -$dis);

$toolChangeLogic = [
  // On rock you can swich between torch and climbing
  '.' . $TORCH => $CLIMBING,
  '.' . $CLIMBING => $TORCH,
  // On wet you can switch between climbing and none
  '=' . $CLIMBING => $NOTHING,
  '=' . $NOTHING => $CLIMBING,
  // And narrow you can use torch or none.
  '|' . $TORCH => $NOTHING,
  '|' . $NOTHING => $TORCH,
];

function calcCost($node, $target_x, $target_y) {
  // cost + remaing distance(minimum possible cost to goal).
  return $node['cost'] + abs($target_x - $node['x']) + abs($target_y - $node['y']);
}

while (!$pq->isEmpty()) {
  $n = $pq->extract();
  $node = $n['data'];
  $pri = $n['priority'];

  $x = $node['x'];
  $y = $node['y'];

  if ($x == $target_x && $y == $target_y && $node['tool'] == $TORCH) {
    // This is the winner.
    echo("Win condition met\n");
    break;
  }
  if (isset($visited[$y][$x][$node['tool']])) {
    // Already been here with this tool, not worth exploring.
    continue;
  }
  if ($node['cost'] > 1500) {
    // Escape condition if it gets too expensive.
    echo("Escaped due to cost too high\n");
    break;
  }

  $visited[$y][$x][$node['tool']] = true;
  // In case we need to go outside the bounds, use calculateErosionLevel.
  $loc = calculateErosionLevel($x, $y, $depth, $erosionLevels) % 3;
  $loc = $types[$loc];
  echo("On $x, $y ($loc) with " . $node['tool'] . " cost " . $node['cost'] . " pri " . $pri . "\n");
  // Options?
  // Change tool +7
  $tool = $toolChangeLogic[$loc . $node['tool']];
  if (empty($tool)) {
    throw new Exception("No tool for " . $loc . $node['tool']);
  }
  $changeTool = [
    'x' => $node['x'],
    'y' => $node['y'],
    'cost' => $node['cost'] + 7,
    'tool' => $tool,
  ];
  $pq->insert($changeTool, -calcCost($changeTool, $target_x, $target_y));

  $right = calculateErosionLevel($x+1, $y, $depth, $erosionLevels) % 3;
  $right = $types[$right];

  // Can we use the tool we have currently to move right?
  if (isset($toolChangeLogic[$right . $node['tool']])) {
    $rightNode = [
      'x' => $node['x'] + 1,
      'y' => $node['y'],
      'cost' => $node['cost'] + 1,
      'tool' => $node['tool'],
    ];
    $pq->insert($rightNode, -calcCost($rightNode, $target_x, $target_y));
  }

  $down = calculateErosionLevel($x, $y + 1, $depth, $erosionLevels) % 3;
  $down = $types[$down];
  // Can we use the tool we have currently to move down?
  if (isset($toolChangeLogic[$down . $node['tool']])) {
    $rightNode = [
      'x' => $node['x'],
      'y' => $node['y'] + 1,
      'cost' => $node['cost'] + 1,
      'tool' => $node['tool'],
    ];
    $pq->insert($rightNode, -calcCost($rightNode, $target_x, $target_y));
  }

  if ($x > 1) {
    $left = calculateErosionLevel($x-1, $y, $depth, $erosionLevels) % 3;
    $left = $types[$left];

    // Can we use the tool we have currently to move left?
    if (isset($toolChangeLogic[$left . $node['tool']])) {
      $leftNode = [
        'x' => $node['x'] - 1,
        'y' => $node['y'],
        'cost' => $node['cost'] + 1,
        'tool' => $node['tool'],
      ];
      $pq->insert($leftNode, -calcCost($leftNode, $target_x, $target_y));
    }
  }

  if ($y > 1) {
    $up = calculateErosionLevel($x, $y - 1, $depth, $erosionLevels) % 3;
    $up = $types[$up];

    // Can we use the tool we have currently to move left?
    if (isset($toolChangeLogic[$up . $node['tool']])) {
      $upNode = [
        'x' => $node['x'],
        'y' => $node['y'] - 1,
        'cost' => $node['cost'] + 1,
        'tool' => $node['tool'],
      ];
      $pq->insert($upNode, -calcCost($upNode, $target_x, $target_y));
    }
  }

  // Add other nodes?
}

echo("Last Node = ");
print_r($node);

echo("\n");

echo("Part 2: " . $node['cost'] . "\n");
