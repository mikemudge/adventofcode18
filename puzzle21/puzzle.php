<?php
$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
// $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

function addr($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a] + $before[$b];
  return $result;
}

function addi($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a] + $b;
  return $result;
}

function mulr($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a] * $before[$b];
  return $result;
}

function muli($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a] * $b;
  return $result;
}

function banr($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a] & $before[$b];
  return $result;
}

function bani($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a] & $b;
  return $result;
}

function borr($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a] | $before[$b];
  return $result;
}

function bori($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a] | $b;
  return $result;
}

function setr($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a];
  return $result;
}

function seti($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $a;
  return $result;
}

function gtir($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $a > $before[$b] ? 1 : 0;
  return $result;
}

function gtri($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a] > $b ? 1 : 0;
  return $result;
}

function gtrr($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a] > $before[$b] ? 1 : 0;
  return $result;
}

function eqir($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $a === $before[$b] ? 1 : 0;
  return $result;
}

function eqri($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a] === $b ? 1 : 0;
  return $result;
}

function eqrr($before, $a, $b, $c) {
  $result = $before;
  $result[$c] = $before[$a] === $before[$b] ? 1 : 0;
  return $result;
}


// This is a program deduced from the input.
$reg1 = 65536;

$reg4 = 16031208;
while(true) {
  $reg3 = $reg1 & 255;
  $reg4 += $reg3;
  $reg4 = $reg4 % 16777216;
  $reg4 *= 65899;
  $reg4 = $reg4 % 16777216;

  echo ($reg1 . "\n");
  if ($reg1 < 256) {
    break;
  }

  $reg1 = floor($reg1 / 256);
}

echo("Part 1: $reg4\n");

$seen = [];
// This is a program deduced from the input.
$reg4 = 0;
while (true) {
  $reg1 = $reg4 | 65536;
  if ($reg1 == 5885821) {
    echo("Got 5885821 from $reg4\n");
  }
  // echo('reg 1 = ' . $reg1 . " => ");
  $orig_reg1 = $reg1;
  if (isset($seen[$reg1])) {
    echo("seen $reg1 before, it got " . $seen[$reg1] . "\n");
    // This indicates the the program would loop forever after this point.
    // We want the last $reg4 before this happened.
    break;
  }

  $reg4 = 16031208;
  while(true) {
    $reg3 = $reg1 & 255;
    $reg4 += $reg3;
    $reg4 = $reg4 % 16777216;
    $reg4 *= 65899;
    $reg4 = $reg4 % 16777216;

    if ($reg1 < 256) {
      break;
    }

    $reg1 = floor($reg1 / 256);
  }

  $seen[$orig_reg1] = $reg4;
  // We are looking for the last value which isn't a duplicate?
  // echo('reg 4 = ' . $reg4 . "\n");
}

// print_r($seen);

echo("Part 2: $reg4\n");
