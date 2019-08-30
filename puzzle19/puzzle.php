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

for($i=0; $i<count($lines); $i++) {
  $line = $lines[$i];
  $parts = explode(" ", $line);
  $instr = array_shift($parts);
  echo ($i  . ": " . $instr . " -> " . implode(" ", $parts) . "\n");
  if ($instr == "#ip") {
    $ip = $parts[0];
  } else {
    $instructions[] = [
      'func' => $instr,
      'args' => $parts
    ];
  }
}

$registers = [0, 0, 0, 0, 0, 0];
$i = 0;
$idx = 0;
while(true) {
  $i++;
  $registers[$ip] = $idx;
  if ($idx < 0 || $idx >= count($instructions)) {
    break;
  }
  $line = $instructions[$idx];
  // echo($i . ": " . $idx . " " . $line['func'] . " " . implode(" ", $line['args']) . "[" . implode(", ", $registers) . "]\n");
  $registers = $line['func']($registers, $line['args'][0], $line['args'][1], $line['args'][2]);
  $idx = $registers[$ip];
  $idx++;
}

echo("Part 1: " . $registers[0] . "\n");

$r4 = 947;
$r0 = 0;
for ($r1 = 1; $r1 <= $r4; $r1++) {
  if ($r4 % $r1 == 0) {
    $r0 += $r1;
  }
}

echo("Part 1: " . $r0 . "\n");

// Interpreted the program manually and found out it was summing the factors of a large number.
$r4 = 10551347;
$r0 = 0;
for ($r1 = 1; $r1 <= $r4; $r1++) {
  if ($r4 % $r1 == 0) {
    $r0 += $r1;
  }
}

echo("Part 2: " . $r0 . "\n");
