<?

function capitalOf($a, $b) {
  return $a != $b && strtoupper($b) == strtoupper($a);
}

function reduce($line) {
  $len = strlen($line);
  for ($i=1; $i<$len; $i++) {
    // echo("$i ${line[$i]}" . "\n");
    if (capitalOf($line[$i], $line[$i-1])) {
      // Remove both.
      // Also move back a space incase the new adjacent pieces match.
      // We will still ++ so -= 2 is right.
      $line = substr($line, 0, $i - 1) . substr($line, $i + 1);
      $i -= 2;
      $len -= 2;
    }
  }
  return $line;
}

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$data = file_get_contents($filename);
$lines = explode("\n", $data);
$line = $lines[0];

// TODO reduce line to the smallest thing.
// Sample.
// $line = "dabAcCaCBAcCcaDA";
$line = reduce($line);
$len = strlen($line);
echo("Part 1: " . $len . "\n");

$start = $line;
$letters = range('a', 'z');

$results = [];
foreach($letters as $letter) {
  $test = str_replace($letter, "", $start);
  $test = str_replace(strtoupper($letter), "", $test);
  $test = reduce($test);
  $len = strlen($test);
  $results[$letter] = $len;
}

asort($results);
echo("Part 2: " . $results[key($results)] . "\n");
