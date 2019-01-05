<?php

function testState($state, $idx, $match, $e=false) {
  for ($i=0;$i<count($match); $i++) {
    if ($e) {
      echo ("check ${match[$i]} == ${state[$idx + $i]}\n");
    }
    if ($state[$idx + $i] !== $match[$i]) {
      if ($e) {
        echo ("no match at $idx, $i, ${match[$i]}\n");
      }
      if ($i >= 4) {
        echo ("no match at $idx, $i - " . showState($state, $idx, count($match)) . "\n");
      }
      return $i;
    }
  }
  return true;
}

function showState($state, $idx, $len) {
  $result = "";
  for ($ii=0;$ii<$len; $ii++) {
    $result .= $state[$idx + $ii];
  }
  return $result;
}

// Needs php7 for part 2 due to memory efficiency.
// php5 can't find the answer with 3G of memory.
$input = 440231;
// $input = 59414;
$match = array_map('intval', str_split("$input"));
$len = count($match);

$state = [3,7];
$totalrecipes = 2;
$elf1idx = 0;
$elf2idx = 1;

echo(join(" ", $match) . "\n");

for ($i=0;$i<=100000000; $i++) {
  $newrecipes = $state[$elf1idx] + $state[$elf2idx];
  if ($newrecipes > 9) {
    $state[] = intval(floor($newrecipes / 10));

    $totalrecipes++;
    if (testState($state, $totalrecipes - $len, $match) === true) {
      echo("Part 2a: " . ($totalrecipes - $len) . "\n");
      break;
    }
  }

  $state[] = intval($newrecipes % 10);
  $totalrecipes++;

  if (testState($state, $totalrecipes - $len, $match) === true) {
    echo("Part 2a: " . ($totalrecipes - $len) . "\n");
    break;
  }

  // Now move the elf indicies.
  $elf1idx = ($elf1idx + 1 + $state[$elf1idx]) % $totalrecipes;
  $elf2idx = ($elf2idx + 1 + $state[$elf2idx]) % $totalrecipes;

  if ($i<10) {
    echo("$elf1idx, $elf2idx \t" . join(",", $state) . "\n");
  }
  if ($i % 100000 == 0) {
    echo("$i turns\n");
  }
  if ($totalrecipes > $input + 10 && $totalrecipes < $input + 12) {
    $result = showState($state, $input, 10);
    echo("Part 1: " . $result . "\n");
    // break;
  }
}
