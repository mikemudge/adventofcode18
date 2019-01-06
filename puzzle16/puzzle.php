<?php
$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
// $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample";
// $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample2";
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

function checkOperations($before, $action, $after) {
  $a = $action[1];
  $b = $action[2];
  $c = $action[3];
  $possible = [];
  $options = ['addr', 'addi', 'mulr', 'muli', 'banr', 'bani', 'borr', 'bori', 'setr', 'seti', 'gtir', 'gtri', 'gtrr', 'eqir', 'eqri', 'eqrr'];
  foreach($options as $func) {
    $result = $func($before, $a, $b, $c);
    if ($after === $result) {
      $possible[] = $func;
    }
  }
  return $possible;
}

$options = ['addr', 'addi', 'mulr', 'muli', 'banr', 'bani', 'borr', 'bori', 'setr', 'seti', 'gtir', 'gtri', 'gtrr', 'eqir', 'eqri', 'eqrr'];
$opcodes = array_fill(0, 16, $options);
$total = 0;
for($i=0;$i<count($lines); $i+=4) {
  $line = $lines[$i];
  $parts = explode(" ", $line);
  if ($parts[0] != "Before:") {
    $lastIndex = $i;
    break;
  }

  $before = [
    intval(substr($parts[1], 1)),
    intval($parts[2]),
    intval($parts[3]),
    intval(substr($parts[4], 0, -1))
  ];
  $line = $lines[$i + 1];
  $parts = explode(" ", $line);
  $action = [
    intval($parts[0]),
    intval($parts[1]),
    intval($parts[2]),
    intval($parts[3])
  ];
  $line = $lines[$i + 2];
  $parts = explode(" ", $line);
  $after = [
    intval(substr($parts[2], 1)),
    intval($parts[3]),
    intval($parts[4]),
    intval(substr($parts[5], 0, -1))
  ];
  $possibles = checkOperations($before, $action, $after);
  $opcode = $action[0];
  if ($opcode == 14) {
    echo("Before " . implode(",", $before) . "\n");
    echo("After  " . implode(",", $after) . "\n");
    echo("Action " . implode(",", $action) . "\n");
    echo(($i / 4) . ": opcode = $opcode " . implode(",", $possibles) . "\n");
  }
  if (!isset($opcodes[$opcode])) {
    $opcodes[$opcode] = $possibles;
  }
  $opcodes[$opcode] = array_intersect($possibles, $opcodes[$opcode]);
  if (count($possibles) >= 3) {
    $total++;
  }
}

$mappedCodes = [];
foreach($opcodes as $opcode=>$possible) {
  $mappedCodes[$opcode] = array_fill_keys($possible, 1);
}

$opcodes = $mappedCodes;
foreach($opcodes as $opcode=>$possible) {
  echo($opcode . " = " . implode(",", array_keys($possible)) . "\n");
}

// Iterate a max number of times.
foreach($opcodes as $it) {
  $removable = [];
  foreach($opcodes as $opcode=>$possible) {
    if (count($possible) == 1) {
      $name = key($possible);
      echo("$name = $opcode because its the only option left\n");
      $known[$name] = $opcode;
      $removable[$name] = 1;
      // Remove this opcode.
      unset($opcodes[$opcode]);
    }
  }

  if (!empty($removable)) {
    foreach($opcodes as $opcode=>&$possible) {
      $possible = array_diff_key($possible, $removable);
    }
    // Weird php issue where if you reuse the named variable it is still a "pointer" to the last value
    // in the array and therefore will replace that value. Not expected behaviour.
    unset($possible);
  }

  // Find any named opcodes which only exist once.
  $usedOpcodes = [];
  foreach($opcodes as $opcode=>$possible) {
    foreach($possible as $name=>$one) {
      if (!isset($usedOpcodes[$name])) {
        $usedOpcodes[$name] = [];
      }
      $usedOpcodes[$name][] = $opcode;
    }
  }

  foreach($usedOpcodes as $name=>$values) {
    if (count($values) == 1) {
      $opcode = $values[0];
      // which number is it?
      echo("$name = $opcode because its the only place its used\n");
      $known[$name] = $opcode;
      // Remove that opcode from the set.
      unset($opcodes[$opcode]);
    }
  }
}
// Calculate which opcode each number is?

echo("Part 1: $total\n");
print_r($known);
$opcodeMap = array_flip($known);
print_r($opcodeMap);

$registers = [0, 0, 0, 0];
for($i=$lastIndex + 2;$i<count($lines); $i++) {
  $line = $lines[$i];
  $parts = explode(" ", $line);
  $opcode = intval($parts[0]);
  $a = intval($parts[1]);
  $b = intval($parts[2]);
  $c = intval($parts[3]);

  $func = $opcodeMap[$opcode];
  $registers = $func($registers, $a, $b, $c);
}

echo("Part 2: " . $registers[0] . "\n");
