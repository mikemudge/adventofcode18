<?php

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample";
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
  private $data;
  private $edges;

  function __construct($data) {
    $this->data = $data;
    $this->edges = [];
  }

  function addEdge($node) {
    $this->edges[] = $node;
  }
}

// First lets find bot which share a region in range.
foreach($nanoBots as $id=>$n) {
  $nodes[$n['id']] = new Node($n);
}
print_r($nodes);

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
      echo($n1['id'] . " can see " . $n2['id'] . "\n");
    }
  }
}

// Find the biggest clique?
foreach ($nodes as $node) {
  // Clique finder?
  // for each child see if there is a clique?

}
