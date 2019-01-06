<?php
$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
// $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample";
// $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "sample2";
$data = file_get_contents($filename);
$lines = explode("\n", $data);

class Player {
  public $type;
  public $health;
  public $attack;
  public $x;
  public $y;

  function __construct($type, $x, $y, $attack) {
    $this->type = $type;
    $this->health = 200;
    $this->attack = $attack;
    $this->x = intval($x);
    $this->y = intval($y);
  }

  public function getAttackTarget($map) {
    $options = $map->getOpenAdjacent($this->x, $this->y);
    $attack = null;
    foreach($options as $cell) {
      if (isset($map->players[$cell['y']][$cell['x']])) {
        $p = $map->players[$cell['y']][$cell['x']];
        if ($p->type != $this->type) {
          if (empty($attack) || $p->health < $attack->health) {
            $attack = $p;
          }
        }
      }
    }
    return $attack;
  }

  public function takeTurn($map) {
    // Check targets before you try and move.
    $attack = $this->getAttackTarget($map);
    if (!$attack) {
      // Move to a target.
      // Find targets.
      $targets = $map->getTargets($this);
      $moveDir = $this->findPath($targets, $map);
      if ($moveDir === 0) $this->y--;
      if ($moveDir === 1) $this->x--;
      if ($moveDir === 2) $this->x++;
      if ($moveDir === 3) $this->y++;

      // Check if you can attack now.
      $attack = $this->getAttackTarget($map);
    }

    if ($attack) {
      // Attack
      $attack->health -= $this->attack;
      if ($attack->health <= 0) {
        // Remove player from the game if it dies.
        echo("Player at " . $attack->y . ", " . $attack->x . " died\n");
        unset($map->players[$attack->y][$attack->x]);
      }
    }
  }

  private function findPath($targets, $map) {
    $visited = [];
    $possible = [[
      'y' => $this->y,
      'x' => $this->x
    ]];

    for ($i=1; $i < 100; $i++) {
      $next = [];
      $bestValue = null;
      $bestDir = null;
      foreach ($possible as $cell) {
        // Try each direction.
        for ($dir=0; $dir < 4; $dir++) {
          $x = $cell['x'];
          $y = $cell['y'];
          if (isset($cell['dir'])) {
            $firstDir = $cell['dir'];
          } else {
            $firstDir = $dir;
          }
          // Order here is reading order.
          if ($dir == 0) $y--;
          if ($dir == 1) $x--;
          if ($dir == 2) $x++;
          if ($dir == 3) $y++;
          if (isset($targets[$y][$x])) {
            // Found a possible best direction to move.
            // Calculate the location value in reading order so we can find the best.
            $value = $y * $map->width + $x;
            if ($bestValue === null || $value < $bestValue) {
              $bestDir = $firstDir;
              $bestValue = $value;
            }
          }
          if ($map->isAvailable($x,$y)) {
            if (empty($visited[$y][$x])) {
              $next[] = [
                'dir' => $firstDir,
                'x' => $x,
                'y' => $y
              ];
              $visited[$y][$x] = $i;
            }
          }
        }
      }
      if ($bestDir !== null) {
        return $bestDir;
      }
      $possible = $next;
      if (empty($possible)) {
        // No targets accessible.
        return null;
      }
    }
    // No path found after 100 steps.
    throw new Exception("No paths after $i steps");
  }
}

class Map {
  public $completed = false;
  public $grid;
  public $players;

  function __construct($grid, $players) {
    $this->grid = $grid;
    $this->players = $players;
    $this->width = count($grid[0]);
  }

  public function isAvailable($x, $y) {
    if (!isset($this->grid[$y][$x])) {
      // It's outside the grid.
      echo("outside grid $x, $y\n");
      return false;
    }
    if ($this->grid[$y][$x] == '#') {
      // It's a wall.
      return false;
    }
    if (isset($this->players[$y][$x])) {
      // Some one else is on this spot.
      return false;
    }
    return true;
  }

  public function getOpenAdjacent($x, $y) {
    $results = [];
    // Return in reading order.
    if ($y > 0 && $this->grid[$y - 1][$x] == '.') {
      $results[] = [
        'y' => $y - 1,
        'x' => $x
      ];
    }
    if ($x > 0 && $this->grid[$y][$x - 1] == '.') {
      $results[] = [
        'y' => $y,
        'x' => $x - 1
      ];
    }
    if ($this->grid[$y][$x + 1] == '.') {
      $results[] = [
        'y' => $y,
        'x' => $x + 1
      ];
    }
    if ($this->grid[$y + 1][$x] == '.') {
      $results[] = [
        'y' => $y + 1,
        'x' => $x
      ];
    }

    return $results;
  }

  public function getTargets($forPlayer) {
    if ($forPlayer->type == 'E') {
      $char = 'G';
    } else {
      $char = 'E';
    }

    $adjacent = [];
    $noneLeft = true;
    foreach ($this->players as $y=>$row) {
      foreach ($row as $x=>$player) {
        if ($player->type === $char) {
          $noneLeft = false;
          $options = $this->getOpenAdjacent($player->x, $player->y);
          foreach($options as $cell) {
            if (isset($this->players[$cell['y']][$cell['x']])) {
              // Already a player at this square.
            } else {
              if (empty($adjacent[$y][$x])) {
                // If this is already set by an earlier player target leave it.
                // This means we will have the first player in reading order set for each adjacent cell.
                $adjacent[$y][$x] = $player;
              }
            }
          }
        }
      }
    }

    if ($noneLeft) {
      echo("No targets found, combat complete\n");
      $this->completed = true;
    }

    return $adjacent;
  }

  public function takeTurn() {
    ksort($this->players);
    foreach ($this->players as $y=>$row) {
      if (empty($this->players[$y])) {
        unset($this->players[$y]);
      }
      ksort($row);
      foreach ($row as $x=>$player) {
        if ($player->health <= 0) {
          // Player died before its turn;
          continue;
        }
        $player->takeTurn($this);
        if ($this->completed) {
          return;
        }
        // Remove player and add it in its new location.
        unset($this->players[$y][$x]);
        $this->players[$player->y][$player->x] = $player;
      }
    }
  }

  public function getTotalHealth() {
    $total = 0;
    foreach ($this->players as $y=>$row) {
      foreach ($row as $x=>$player) {
        $total += $player->health;
      }
    }
    return $total;
  }

  public function getElfCount() {
    $total = 0;
    foreach ($this->players as $y=>$row) {
      foreach ($row as $x=>$player) {
        if ($player->type === 'E') {
          $total++;
        }
      }
    }
    return $total;
  }

  public function printMap() {
    ksort($this->players);
    foreach ($this->grid as $y=>$row) {
      foreach ($row as $x => $value) {
        if (isset($this->players[$y][$x])) {
          echo($this->players[$y][$x]->type);
        } else {
          echo($value);
        }
      }
      if (isset($this->players[$y])) {
        ksort($this->players[$y]);
        foreach ($this->players[$y] as $player) {
          echo (" " . $player->type . "(" . $player->health . ")");
        }
      }
      echo("\n");
    }
  }
}

// Part 1 This should be 3.
// Part 2 Binary search this value to see when elves win without casulties.
// 13 is the lowest which allows victory without a casualty.
$elfAttack = 13;
$grid = [];
$elfCount = 0;
$goblinCount = 0;
foreach ($lines as $y=>$line) {
  $row = [];
  foreach (str_split($line) as $x => $value) {
    if ($value == 'E' || $value == "G") {
      // Add goblins and elves.
      if ($value == 'E') {
        $players[$y][$x] = new Player($value, $x, $y, $elfAttack);
        $elfCount++;
      } else {
        $players[$y][$x] = new Player($value, $x, $y, 3);
        $goblinCount++;
      }
      $value = ".";
    }
    $row[] = $value;
  }
  $grid[] = $row;
}

$map = new Map($grid, $players);
$map->printMap();

// Play a number of turns.
for ($i=1; $i <= 100; $i++) {
  echo("After $i rounds\n");
  $map->takeTurn();
  $map->printMap();
  if ($map->completed) {
    $value = $map->getTotalHealth() * ($i - 1);
    echo("Part 1: " . $map->getTotalHealth() . " * " . ($i - 1) . " = $value\n");
    $elfCasulties = $elfCount - $map->getElfCount();
    if ($elfCasulties === 0) {
      echo("Part 2: elves win with " . $elfAttack . "\n");
    } else {
      echo("Part 2: elves lose with " . $elfAttack . "\n");
    }
    break;
  }
}

if (!$map->completed) {
  echo("No result after $i turns\n");
}