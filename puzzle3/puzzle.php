<?

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

$grid = [];
foreach($lines as $line) {
  echo($line . "\n");
  $parts = split(" ", $line);
  $loc = split(",", substr($parts[2], 0, -1));
  $size = split("x", $parts[3]);
  for ($y = $loc[1]; $y < $loc[1] + $size[1]; $y++) {
    if (empty($grid[$y])) {
      $grid[$y] = [];
    }
    for ($x = $loc[0]; $x < $loc[0] + $size[0]; $x++) {
      if (empty($grid[$y][$x])) {
        $grid[$y][$x] = 1;
      } else {
        // Existing
        $grid[$y][$x]++;
      }
    }
  }
}
$count = 0;
foreach ($grid as $k=>$row) {
  foreach($row as $k2=>$sqr) {
    if ($sqr > 1) {
      $count++;
    }
  }
}
echo("Part 1: " . $count . "\n");

foreach($lines as $line) {
  $parts = split(" ", $line);
  $loc = split(",", substr($parts[2], 0, -1));
  $size = split("x", $parts[3]);
  $notit = false;
  for ($y = $loc[1]; $y < $loc[1] + $size[1]; $y++) {
    for ($x = $loc[0]; $x < $loc[0] + $size[0]; $x++) {
      if ($grid[$y][$x] != 1) {
        $notit = true;
        break;
      }
    }
    if ($notit) {
      break;
    }
  }
  if (!$notit) {
    echo("Found it\n");
    echo($line . "\n");
    break;
  }
}


