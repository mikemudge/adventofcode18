<?php

class Group {
  public $units;
  public $info;
  public $weak = [];
  public $immune = [];

  function __construct($data) {
    print("init group");
    $this->units = $data['units'];
    $this->info = $data;
  }
}

function initiative($g1, $g2) {
  return $g2->info['initiative'] - $g1->info['initiative'];
}

function effectivePower($g1, $g2) {
  $result = $g2->units * $g2->info['attack'] - $g1->units * $g1->info['attack'];
  if ($result == 0) {
    // Use initiative if effective power matches.
    return initiative($g1, $g2);
  }
  return $result;
}

function groupId($g) {
  return $g->info['type'] . " group " . $g->info['id'];
}

function printGroup($g) {
  echo(groupId($g) . "\n");
}

function printGroupDetails($g) {
  echo($g->info['type'] . " group " . $g->info['id'] . " - " . $g->units . " units with attack " . $g->info['attackType'] . " " . $g->info['attack'] . " ep " . $g->info['attack'] * $g->units . "\n");
}

function getDamage($attacker, $t) {
  $damage = $attacker->units * $attacker->info['attack'];
  if (in_array($attacker->info['attackType'], $t->weak)) {
    $damage *= 2;
  }
  if (in_array($attacker->info['attackType'], $t->immune)) {
    $damage *= 0;
  }
  return $damage;
}

/* Find and remove the best target from a pool of possible targets. */
function findBestTarget($attacker, &$list) {
  $bestTarget = null;
  $bestIndex = -1;
  $mostDamage = 0;
  foreach ($list as $i=>$t) {
    $damage = getDamage($attacker, $t);
    echo("could deal $damage to ");
    printGroup($t);

    if ($damage === 0) {
      // Don't consider targets you can't damage.
      continue;
    }
    if ($damage > $mostDamage) {
      $bestTarget = $t;
      $mostDamage = $damage;
      $bestIndex = $i;
    } elseif ($damage === $mostDamage) {
      if (effectivePower($bestTarget, $t) > 0) {
        // Use effectivePower as a decider when you can deal the same damage to multiple groups.
        $bestTarget = $t;
        $bestIndex = $i;
        $mostDamage = $damage;
      }
    }
  }
  if ($bestIndex > -1) {
    echo("Picked ");
    printGroup($bestTarget);
    unset($list[$bestIndex]);
    return $bestTarget;
  }
  return null;
}

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
// $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

function doPuzzle($immuneBuff) {
  global $lines;
  $list = [];
  $groupType = 'immune';
  $groupIds = [
    'immune' => 1,
    'infection' => 1
  ];
  foreach($lines as $line) {
    echo($line. "\n");
    if ($line == '' || $line == "Immune System:") {
      continue;
    }
    if ($line == "Infection:") {
      // Change which list we will add the groups to.
      $groupType = 'infection';
    }

    $matches = [];
    // 3609 units each with 2185 hit points (weak to cold, radiation) with an attack that does 5 slashing damage at initiative 20
    if (preg_match('/^(\d+) units each with (\d+) hit points (\(.*\) )?with an attack that does (\d+) (\w+) damage at initiative (\d+)$/', $line, $matches)) {;
      $group = new Group([
        'id' => $groupIds[$groupType]++,
        'type' => $groupType,
        'units' => $matches[1],
        'hp' => $matches[2],
        'attack' => $matches[4] + ($groupType == 'immune' ? $immuneBuff : 0),
        'attackType' => $matches[5],
        'initiative' => $matches[6],
        'weak' => [],
        'immune' => []
      ]);

      // Remove the ( and ) plus a space from the string.
      $strengthWeaknesses = substr($matches[3], 1, -2);
      if ($strengthWeaknesses) {
        $parts = explode("; ", $strengthWeaknesses);
        foreach($parts as $part) {
          $pieces = explode(" ", $part);
          if ($pieces[0] == 'weak') {
            $group->weak = explode(",", implode("",array_slice($pieces, 2)));
          } elseif($pieces[0] == 'immune') {
            $group->immune = explode(",", implode("",array_slice($pieces, 2)));

          }
        }
      }

      print_r($group);
      $list[] = $group;
    } else {
      echo("Error parsing line $line\n");
    }
  }

  $iter = 0;
  while(true) {
    $iter++;
    // Target phase
    $possibleTargets = [
      'immune' => [],
      'infection' => []
    ];
    foreach($list as $g) {
      if ($g->units < 0)continue;
      $possibleTargets[$g->info['type']][] = $g;
    }
    unset($g);
    // The fight is over if either side has no groups left.
    if (empty($possibleTargets['immune'])) {
      break;
    }
    if (empty($possibleTargets['infection'])) {
      break;
    }

    usort($list, 'effectivePower');
    // Need to proceed in order.
    echo("\nTargeting phase $iter\n");

    foreach($list as $g) {
      if ($g->units < 0)continue;
      echo("\n");
      printGroup($g);
      // Find a target.
      if ($g->info['type'] == 'immune') {
        $g->target = findBestTarget($g, $possibleTargets['infection']);
      } else {
        $g->target = findBestTarget($g, $possibleTargets['immune']);
      }
    }
    unset($g);

    echo("\nAttacking phase $iter\n");
    usort($list, 'initiative');
    $totalUnitsKilled = 0;
    foreach($list as $g) {
      if ($g->units < 0)continue;
      if (empty($g->target)) {
        echo(groupId($g) . " not attacking\n");
        continue;
      }
      $damage = getDamage($g, $g->target);
      $unitsKilled = floor($damage / $g->target->info['hp']);
      $unitsKilled = min($unitsKilled, $g->target->units);
      echo(groupId($g) . " attacks " . groupId($g->target) . " killed $unitsKilled units\n");
      $g->target->units -= $unitsKilled;
      $totalUnitsKilled += $unitsKilled;
    }
    unset($g);

    echo("\nEnd of round\n");
    $removes = [];
    foreach($list as $i=>$g) {
      if ($g->units <= 0) {
        $removes[] = $i;
      }
    }
    foreach($removes as $i) {
      echo("removing " . $i . "\n");
      unset($list[$i]);
    }

    foreach($list as $g) {
      printGroupDetails($g);
    }
    if ($totalUnitsKilled == 0) {
      return $list;
    }
  }

  echo("\nEnd of fight\n");

  return $list;
}

$list = doPuzzle(0);
$armies = 0;
foreach($list as $g) {
  printGroupDetails($g);
  $armies += $g->units;
}
echo("Part 1: $armies\n");

// With Buff +43 infection won with 4397 armies left
// With Buff +49 immune won with 1045 armies left
// Anything inbetween fights forever.
$buffs = [43,44,45,46,47,48,49];
// $buffs = [36,37,38,39,40,41,42];
// $buffs = [29,30,31,32,33,34,35];
$buffs = range(0,50);
foreach ($buffs as $b) {
  $list = doPuzzle($b);
  $armies = 0;
  $stalemate = false;
  foreach($list as $g) {
    if ($g->info['type'] != $list[0]->info['type']) {
      $stalemate = true;
    }
    printGroupDetails($g);
    $armies += $g->units;
  }
  if ($stalemate) {
    $results[] = "Stalemate with buff +$b";
  } else {
    $results[] = "With Buff +$b " . $list[0]->info['type'] . " won with $armies armies left";
  }
}

foreach ($results as $r){
  print("$r\n");
}

// With Buff +0 infection won with 15493 armies left
// With Buff +1 infection won with 15332 armies left
// With Buff +2 infection won with 15194 armies left
// With Buff +3 infection won with 14980 armies left
// With Buff +4 infection won with 14857 armies left
// With Buff +5 infection won with 14731 armies left
// With Buff +6 infection won with 14487 armies left
// With Buff +7 infection won with 14252 armies left
// With Buff +8 infection won with 14101 armies left
// With Buff +9 infection won with 13957 armies left
// With Buff +10 infection won with 13838 armies left
// With Buff +11 infection won with 13678 armies left
// With Buff +12 infection won with 13513 armies left
// With Buff +13 infection won with 13280 armies left
// With Buff +14 infection won with 13107 armies left
// With Buff +15 infection won with 12921 armies left
// With Buff +16 infection won with 12730 armies left
// With Buff +17 infection won with 12531 armies left
// With Buff +18 infection won with 12261 armies left
// With Buff +19 infection won with 11637 armies left
// With Buff +20 infection won with 11360 armies left
// With Buff +21 infection won with 11106 armies left
// With Buff +22 infection won with 10847 armies left
// With Buff +23 infection won with 10623 armies left
// With Buff +24 infection won with 10364 armies left
// With Buff +25 infection won with 10112 armies left
// With Buff +26 infection won with 9877 armies left
// With Buff +27 infection won with 9487 armies left
// With Buff +28 infection won with 9187 armies left
// With Buff +29 infection won with 8885 armies left
// With Buff +30 infection won with 8753 armies left
// With Buff +31 infection won with 8522 armies left
// With Buff +32 infection won with 8265 armies left
// With Buff +33 infection won with 8029 armies left
// With Buff +34 infection won with 7757 armies left
// With Buff +35 infection won with 7510 armies left
// With Buff +36 infection won with 7177 armies left
// With Buff +37 infection won with 6905 armies left
// With Buff +38 infection won with 6651 armies left
// With Buff +39 infection won with 6375 armies left
// With Buff +40 infection won with 6100 armies left
// With Buff +41 infection won with 5670 armies left
// With Buff +42 infection won with 5208 armies left
// With Buff +43 infection won with 4397 armies left
// Stalemate with buff +44
// Stalemate with buff +45
// Stalemate with buff +46
// Stalemate with buff +47
// Stalemate with buff +48
// With Buff +49 immune won with 1045 armies left
// With Buff +50 immune won with 1280 armies left
// 49 is wrong.
// 48 is wrong.
echo("Part 2: $armies\n");
