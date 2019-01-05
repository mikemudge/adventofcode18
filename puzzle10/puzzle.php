<?php
$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

$particles = [];
foreach ($lines as $line) {
  preg_match('/position=<\\s*(-?\d+),\\s*(-?\d+)> velocity=<\\s*(-?\d+),\\s*(-?\d+)>/', $line, $matches, PREG_OFFSET_CAPTURE);
  $particles[] = [
    'x0' => intval($matches[1][0]),
    'y0' => intval($matches[2][0]),
    'vx' => intval($matches[3][0]),
    'vy' => intval($matches[4][0])
  ];
}

function getpoints($particles, $t) {
  $xs = [];
  $ys = [];
  foreach ($particles as $p) {
    $xs[] = $p['x0'] + $p['vx'] * $t;
    $ys[] = $p['y0'] + $p['vy'] * $t;
  }
  return [$xs, $ys];
}

$p = $particles[0];
print_r($p);
$bestarea = 0;
for ($t=10000; $t<11000; $t++) {
  list($xs, $ys) = getpoints($particles, $t);
  $w = (max($xs) - min($xs));
  $h = (max($ys) - min($ys));

  $area = $w * $h;
  if ($bestarea == 0 || $area < $bestarea) {
    $bestarea = $area;
    $besttime = $t;
  }
  if ($t % 1000 == 0) {
    echo("Range at $t is " . $w . "x" . $h . "=$area\n");
  }
}
list($xs, $ys) = getpoints($particles, $besttime);
$w = (max($xs) - min($xs));
$h = (max($ys) - min($ys));
$area = $w * $h;

echo("Best range at $besttime is " . $w . "x" . $h . "=$area\n");

$grid = [];
for ($i=0;$i<=$h;$i++) {
  $grid[] = array_fill(0, $w + 1, '.');
}

$min_x = min($xs);
$min_y = min($ys);
for ($i=0;$i<count($xs);$i++) {
  // echo(($ys[$i] - $min_y) . "x" . ($xs[$i] - $min_x) . "\n");
  $grid[$ys[$i] - $min_y][$xs[$i] - $min_x] = '#';
}

echo("Part 1:\n");
for ($i=0;$i<=$h;$i++) {
  echo(implode("", $grid[$i]) . "\n");
}

echo("Part 2: $besttime\n");

/*
######..#....#..#####....####...#####...#####...#....#..#####.
#.......##...#..#....#..#....#..#....#..#....#..#....#..#....#
#.......##...#..#....#..#.......#....#..#....#..#....#..#....#
#.......#.#..#..#....#..#.......#....#..#....#..#....#..#....#
#####...#.#..#..#####...#.......#####...#####...######..#####.
#.......#..#.#..#..#....#..###..#.......#....#..#....#..#..#..
#.......#..#.#..#...#...#....#..#.......#....#..#....#..#...#.
#.......#...##..#...#...#....#..#.......#....#..#....#..#...#.
#.......#...##..#....#..#...##..#.......#....#..#....#..#....#
#.......#....#..#....#...###.#..#.......#####...#....#..#....#
FNRGPBHR
*/
