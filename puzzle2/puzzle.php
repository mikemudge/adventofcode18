<?

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

$twos = 0;
$threes = 0;
foreach($lines as $line) {
  // Array flip will override duplicate counts.
  $letter_counts = array_flip(array_count_values(str_split($line)));
  if (isset($letter_counts[2])) {
    $twos++;
  }
  if (isset($letter_counts[3])) {
    $threes++;
  }
}

echo("Part 1: " . ($twos * $threes) . "\n");

sort($lines);
$length = count($lines);
echo($length . "\n");
for($i = 1; $i < $length; $i++) {
  $a = str_split($lines[$i]);
  $b = str_split($lines[$i - 1]);
  $result = array_diff_assoc($a, $b);
  if (count($result) === 1) {
    echo($i . " " . implode("", $result) . "\n");
    echo(implode("", $a) . "\n" . implode("", $b) . "\n");

    // jbbenqtlavxhivmwyscjukztdp.
    // jbbenqtlagxhivmwyscjukztdp.
    // jbbenqtlaxhivmwyscjukztdp.
  }
}
