<?

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

$grid = [];
sort($lines);
$guard = "";
$sleepat = 0;
foreach($lines as $line) {
  $parts = split(" ", $line);
  $date = substr($parts[0], 1);
  $time = substr($parts[1], 0, -1);
  if ($parts[2] == "Guard") {
    $guard = $parts[3];
    if (empty($sleptfor[$guard])) {
      $sleptfor[$guard] = 0;
      $sleepMinutes[$guard] = array_fill(0, 60, 0);
    }
  } else {
    $minute = split(":", $time)[1];
    if ($parts[2] == 'falls') {
      $sleepat = $minute;
    } else {
      if ($guard) {
        $sleptfor[$guard] += ($minute - $sleepat);
        for ($i = $sleepat; $i < $minute; $i++) {
          $sleepMinutes[$guard][$i]++;
        }
      }
    }
  }
  echo($guard . ": " . $date . " " . $time . " " . $parts[2] . " " . $parts[3] . "\n");
}
arsort($sleptfor);
print_r($sleptfor);
$guard_id = key($sleptfor);

// Now we need the best minute.
arsort($sleepMinutes[$guard_id]);
print_r($sleepMinutes[$guard_id]);
$minute = key($sleepMinutes[$guard_id]);

$answer = $minute * substr($guard_id, 1);
echo("Part 1: $guard_id@$minute" . $answer . "\n");

$bests = [];
foreach ($sleptfor as $guard_id => $value) {
  arsort($sleepMinutes[$guard_id]);
  $bestMinute = key($sleepMinutes[$guard_id]);
  $sleepTimes = intval($sleepMinutes[$guard_id][$bestMinute]);
  $bests[substr($guard_id, 1) * $bestMinute] = $sleepTimes;
  echo "$guard_id $bestMinute $sleepTimes\n";
}

// Now we need the best minute.
arsort($bests);
print_r($bests);
$answer = key($bests);

echo "Part 2: $answer\n";

