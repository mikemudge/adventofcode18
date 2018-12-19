<?
function findClosest($x, $y, $points) {
  $best = $points[0];
  $bestDis = PHP_INT_MAX;
  $draw = False;
  foreach ($points as $point) {
    $dis = abs($x - $point['x']) + abs($y - $point['y']);
    if ($dis == $bestDis) {
      $draw = True;
    } else if ($dis < $bestDis) {
      $best = $point;
      $bestDis = $dis;
      $draw = False;
    }
  }
  if ($draw) {
    return ['letter' => '.'];
  }
  return $best;
}

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

// echo("$minx, $miny, $maxx, $maxy\n");
// 46, 67, 352, 348
$grid = array_fill(0, 400, []);
for($i=0;$i<400;$i++) {
  $grid[$i] = array_fill(0, 400, ".");
}
$letters = array_merge(range('A', 'Z'), range('a', 'z'));
$points = [];
$counts = [];
foreach($lines as $i=>$line) {
  list($x, $y) = split(", ", $line);
  $x = intval($x);
  $y = intval($y);
  $points[] = [
    'x' => $x,
    'y' => $y,
    'letter' => $letters[$i]
  ];
  $counts[$letters[$i]] = 0;
  $grid[$y][$x] = $letters[$i];
}

for ($y=0; $y < 400; $y++) {
  for ($x=0; $x < 400; $x++) {
    $closest = findClosest($x, $y, $points);
    $grid[$y][$x] = $closest['letter'];
    $counts[$closest['letter']]++;
  }
}

for($y=67;$y<150;$y++) {
  echo(substr(implode("", $grid[$y]), 46, 140) . "\n");
}
// All letters on the edges are infinite.
$infiniteLetters = [];
for ($i=0; $i < 400; $i++) {
  $infiniteLetters[$grid[0][$i]] = True;
  $infiniteLetters[$grid[399][$i]] = True;
  $infiniteLetters[$grid[$i][0]] = True;
  $infiniteLetters[$grid[$i][399]] = True;
}
foreach ($infiniteLetters as $key => $value) {
  unset($counts[$key]);
}
arsort($counts);
$answer = key($counts);
echo("Part 1: " . $counts[$answer] . "\n");

// Find all locations which have total manhatten distance to all points of <10000
function totalDistanceToPoints($x, $y, $points) {
  $dis = 0;
  foreach ($points as $point) {
    $dis += abs($x - $point['x']) + abs($y - $point['y']);
  }
  return $dis;
}

$matches = 0;
for ($y=0; $y < 400; $y++) {
  for ($x=0; $x < 400; $x++) {
    $dis = totalDistanceToPoints($x, $y, $points);
    if ($dis < 10000) {
      $matches++;
    }
  }
}
echo("Part 2: " . $matches . "\n");
