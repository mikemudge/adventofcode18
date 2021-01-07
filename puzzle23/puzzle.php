<?php

$input = "input";
// $input = "sample";
$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . $input;
$data = file_get_contents($filename);
$lines = explode("\n", $data);

$nanoBots = [];
$maxRange = 0;
$maxRangeNanoBot;
foreach($lines as $i=>$line) {
  echo($line. "\n");
  $parts = explode(",", substr($line, 5));
  $x = intval($parts[0]);
  $y = intval($parts[1]);
  $z = intval(substr($parts[2], 0, -1));

  $r = intval(substr($parts[3], 3));

  $nanoBot = [
    'id' => $i,
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
class Node {
  public $data;
  public $edges;

  function __construct($data) {
    $this->id = $data['id'];
    $this->data = $data;
    $this->edges = [];
  }

  function addEdge($node) {
    $this->edges[$node->id] = $node;
  }

    function getMin($signs) {
        return $signs[0] * $this->data['x']
            + $signs[1] * $this->data['y']
            + $signs[2] * $this->data['z']
            - $this->data['r'];
    }

    function getMax($signs) {
        return $signs[0] * $this->data['x']
            + $signs[1] * $this->data['y']
            + $signs[2] * $this->data['z']
            + $this->data['r'];
    }
}

// First lets find bot which share a region in range.
foreach($nanoBots as $id=>$n) {
  $nodes[$n['id']] = new Node($n);
}
// print_r($nodes);

foreach($nanoBots as $n1) {
  $node = $nodes[$n1['id']];
  foreach($nanoBots as $n2) {
    if ($n2['id'] == $n1['id']) {
      continue;
    }
    $dis = abs($n1['x'] - $n2['x']);
    $dis += abs($n1['y'] - $n2['y']);
    $dis += abs($n1['z'] - $n2['z']);
    if ($dis <= $n1['r'] + $n2['r']) {
      $node2 = $nodes[$n2['id']];
      $node->addEdge($node2);
      $node2->addEdge($node);
      // echo($n1['id'] . " can see " . $n2['id'] . "\n");
    }
  }
}

$nodeWithMostEdges = null;
// Find the biggest clique?
foreach ($nodes as $node) {
    // Clique finder?
    // for each child see if there is a clique?
    $numEdges = count($node->edges);
    if ($nodeWithMostEdges == null || $numEdges > count($nodeWithMostEdges->edges)) {
        $nodeWithMostEdges = $node;
    }
    echo($node->data['id'] . 'can see ' . $numEdges . ' bots' . PHP_EOL);
    $children = [];
    foreach ($node->edges as $id => $child) {
        $children[] = $child->id;
    }
    // print(join(", ", $children) . PHP_EOL);
}

print("Found a popular node " . $nodeWithMostEdges->id . " with " . count($nodeWithMostEdges->edges) . " edges" . PHP_EOL);

// Find the biggest clique?
$nodesByEdgeCount = [];
foreach ($nodes as $node) {
    $numEdges = count($node->edges);
    if (empty($nodesByEdgeCount[$numEdges])) {
        $nodesByEdgeCount[$numEdges] = 0;
    }
    $nodesByEdgeCount[$numEdges]++;
}

echo(json_encode($nodesByEdgeCount, JSON_PRETTY_PRINT) . PHP_EOL);

$tot = 0;
for($i=0;$i<1000;$i++) {
    if (isset($nodesByEdgeCount[$i])) {
        $tot += $nodesByEdgeCount[$i];
        echo("$i - $tot" . PHP_EOL);
    }
}

$popularNodes = [];
foreach ($nodes as $node) {
    $numEdges = count($node->edges);
    # Assuming we will find a clique with more than 505 nodes.
    # Therefore no nodes with less edges than this can be included.
    if ($input != "sample" && $numEdges <= 505) {
        continue;
    }
    if ($numEdges <= 2) {
        // For sample.
        continue;
    }
    $popularNodes[] = $node;
}

echo("Remaining nodes " . count($popularNodes));

foreach($popularNodes as $n) {
    echo("Look at " . $n->id . PHP_EOL);
    foreach($popularNodes as $nn) {
        if ($n == $nn) {
            continue;
        }
        if (empty($n->edges[$nn->id])) {
            echo("Missing link" . $n->id . " - " . $nn->id . PHP_EOL);
        }
    }
}
// This set looks like a clique, as it has no missing links.
// How do we find a location which can reach all the nodes?
$x = $popularNodes[0]->data['x'];
$y = $popularNodes[0]->data['y'];
$z = $popularNodes[0]->data['z'];
$w = $popularNodes[0]->data['r'];
$h = $popularNodes[0]->data['r'];
$d = $popularNodes[0]->data['r'];
$trackedMin = [
    $popularNodes[0]->getMin([1, 1, 1]),
    $popularNodes[0]->getMin([-1, 1, 1]),
    $popularNodes[0]->getMin([-1, -1, 1])
];
$trackedMax = [
    $popularNodes[0]->getMax([1, 1, 1]),
    $popularNodes[0]->getMax([-1, 1, 1]),
    $popularNodes[0]->getMax([-1, -1, 1])
];

foreach($popularNodes as $i=>$n) {
    $min = $n->getMin([1, 1, 1]);
    $max = $n->getMax([1, 1, 1]);
    $trackedMin[0] = max($min, $trackedMin[0]);
    $trackedMax[0] = min($max, $trackedMax[0]);
    $min = $n->getMin([-1, 1, 1]);
    $max = $n->getMax([-1, 1, 1]);
    $trackedMin[1] = max($min, $trackedMin[1]);
    $trackedMax[1] = min($max, $trackedMax[1]);
    $min = $n->getMin([-1, -1, 1]);
    $max = $n->getMax([-1, -1, 1]);
    $trackedMin[2] = max($min, $trackedMin[2]);
    $trackedMax[2] = min($max, $trackedMax[2]);
}

print("1,1,1 is between $trackedMin[0], $trackedMax[0]" . PHP_EOL);
print("-1,1,1 is between $trackedMin[1], $trackedMax[1]" . PHP_EOL);
print("-1,-1,1 is between $trackedMin[2], $trackedMax[2]" . PHP_EOL);

// x + y + z = 36
// -x + y + z = 12
// -x - y + z = -12

// Find x, y, z?
// 2z = 36 - 12
// z = 12

// -x + y + z - (-x - y + z) = 12 - -12
// 2y = 12 - -12
// 2y = 24
// y = 12

// 2x = 36 - 12
// x = 12

// In real input.
// x + y + z = 113066145
// -x + y + z = 21662988
// -x - y + z = -39122725

$z = ($trackedMin[0] + $trackedMax[2]) / 2;
$y = ($trackedMin[1] - $trackedMin[2]) / 2;
$x = ($trackedMin[0] - $trackedMax[1]) / 2;
print("$x,$y,$z" . PHP_EOL);

print("Part 2: " . (abs($x) + abs($y) + abs($z)) . PHP_EOL);