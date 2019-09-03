<?php

class Star {
  private $coords;
  private $neighbors;
  private $cluster;

  function __construct($coords) {
    $this->cluster = 0;
    $this->coords = $coords;
    $this->neighbors = [];
  }

  function addNeighbor($node) {
    $this->neighbors[] = $node;
  }

  function disTo($star) {
    return abs($this->coords[0] - $star->coords[0])
        + abs($this->coords[1] - $star->coords[1])
        + abs($this->coords[2] - $star->coords[2])
        + abs($this->coords[3] - $star->coords[3]);
  }

  function getCluster() {
    return $this->cluster;
  }

  function createCluster($clusterId) {
    if ($this->cluster !== 0) {
      // Already part of a cluster.
      if ($this->cluster !== $clusterId) {
        throw new Exception("Star has cluster $this->cluster already $clusterId");
      }
      return;
    }
    $this->cluster = $clusterId;
    foreach ($this->neighbors as $n) {
      $n->createCluster($clusterId);
    }
  }
}

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
// $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

$stars = [];

foreach($lines as $line) {
  $coordinates = explode(",", $line);

  $stars[] = new Star($coordinates);
}

foreach ($stars as $star) {
  foreach ($stars as $star2) {
    $dis = $star->disTo($star2);
    if ($star != $star2 && $dis <= 3) {
      // echo($dis . " between stars\n");
      $star->addNeighbor($star2);
    }
  }
}

echo("Finished building graph of stars\n");

$clusters = 0;
// Iterate stars and make sure every one is part of a cluster.
foreach ($stars as $i=>$star) {
  if (!$star->getCluster()) {
    $clusters++;
    echo("Create new cluster for star $i cluster " . $clusters . "\n");
    $star->createCluster($clusters);
  }
}

echo("Part 1: $clusters\n");
