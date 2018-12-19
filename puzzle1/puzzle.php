<?

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

echo("Part 1: " . array_sum($lines) . "\n");
$seen = [];
$value = 0;
$part2 = null;
while(true) {
  foreach($lines as $line) {
    $value += $line;
    if (isset($seen[$value])) {
      $part2 = $value;
      break;
    }
    $seen[$value] = true;
  }
  if ($part2 !== null) {
    break;
  }
}
echo("Part 2: " . $part2 . "\n");
