<?php
$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
// $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

$carts = [];
$numCarts = 0;
foreach($lines as $y => $line) {
  for ($x=0; $x < strlen($line); $x++) {
    echo($line[$x]);
    $grid[$y][$x] = $line[$x];
    if ($line[$x] == "<" || $line[$x] == ">" || $line[$x] == "v" || $line[$x] == "^") {
      $grid[$y][$x] = "|";
      if ($line[$x] == "<" || $line[$x] == ">") {
        $grid[$y][$x] = "-";
      }
      $carts[$y][$x] = [
        'dir' => $line[$x],
        'turns' => 0,
      ];
      $numCarts++;
    }
  }
  echo("\n");
}

// print_r($carts);
// Play 5 turns.
for ($i=0; $i < 100000; $i++) {
  $nextCarts = [];
  ksort($carts);
  foreach ($carts as $y=>$row) {
    ksort($row);
    foreach ($row as $x=>$cart) {
      $nx = $x;
      $ny = $y;
      switch($cart['dir']) {
        case '<':
          $nx = $x - 1;
          if ($grid[$ny][$nx] == '\\') {
            $cart['dir'] = '^';
          }
          if ($grid[$ny][$nx] == '/') {
            $cart['dir'] = 'v';
          }
          if ($grid[$ny][$nx] == '+') {
            if ($cart['turns'] % 3 == 0) {
                $cart['dir'] = 'v';
            }
            if ($cart['turns'] % 3 == 2) {
                $cart['dir'] = '^';
            }
            $cart['turns']++;
          }
          break;
        case '>':
          $nx = $x + 1;
          if ($grid[$ny][$nx] == '\\') {
            $cart['dir'] = 'v';
          }
          if ($grid[$ny][$nx] == '/') {
            $cart['dir'] = '^';
          }
          if ($grid[$ny][$nx] == '+') {
            if ($cart['turns'] % 3 == 0) {
                $cart['dir'] = '^';
            }
            if ($cart['turns'] % 3 == 2) {
                $cart['dir'] = 'v';
            }
            $cart['turns']++;
          }
          break;
        case '^':
          $ny = $y - 1;
          if ($grid[$ny][$nx] == '\\') {
            $cart['dir'] = '<';
          }
          if ($grid[$ny][$nx] == '/') {
            $cart['dir'] = '>';
          }
          if ($grid[$ny][$nx] == '+') {
            if ($cart['turns'] % 3 == 0) {
                $cart['dir'] = '<';
            }
            if ($cart['turns'] % 3 == 2) {
                $cart['dir'] = '>';
            }
            $cart['turns']++;
          }
          break;
        case 'v':
          $ny = $y + 1;
          if ($grid[$ny][$nx] == '\\') {
            $cart['dir'] = '>';
          }
          if ($grid[$ny][$nx] == '/') {
            $cart['dir'] = '<';
          }
          if ($grid[$ny][$nx] == '+') {
            if ($cart['turns'] % 3 == 0) {
                $cart['dir'] = '>';
            }
            if ($cart['turns'] % 3 == 2) {
                $cart['dir'] = '<';
            }
            $cart['turns']++;
          }
          break;
      }

      if (isset($nextCarts[$y][$x])) {
        // A cart crashed into you before you moved.
        // Not using $carts because we can't tell which of those have already been moved.
        echo("Crash occurred at $x,$y\n");
        unset($nextCarts[$y][$x]);
        $numCarts -= 2;
      } else if (isset($nextCarts[$ny][$nx])) {
        // You crashed into another cart.
        // Not using $carts because we can't tell which of those have already moved.
        echo("Crash occurred at $nx,$ny\n");
        unset($nextCarts[$ny][$nx]);
        $numCarts -= 2;
      } else {
        // Only set cart if its not crashed.
        $nextCarts[$ny][$nx] = $cart;
      }

      // Detect collisions?
    }
  }
  $carts = $nextCarts;
  echo("Turn $i: $numCarts carts remain\n");
  if ($numCarts == 1) {
    break;
  }
  // if ($i % 100 == 0) {
  //   foreach ($grid as $y => $row) {
  //     foreach ($row as $x => $cell) {
  //       if (isset($carts[$y][$x])) {
  //         echo($carts[$y][$x]['dir']);
  //       } else {
  //         echo($cell);
  //       }
  //     }
  //     echo("\n");
  //   }
  // }
}

// Get the $x and $y keys from the array.
echo("Part 2: " . key($carts[key($carts)]) . "," . key($carts) . "\n");
