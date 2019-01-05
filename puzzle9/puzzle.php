<?php
class ListNode {
  public $data;
  public $next = null;
  public $prev = null;

  function __construct($data) {
    $this->data = $data;
  }
}
// Samples
// 10 players; last marble is worth 1618 points: high score is 8317
// 13 players; last marble is worth 7999 points: high score is 146373
// 17 players; last marble is worth 1104 points: high score is 2764
// 21 players; last marble is worth 6111 points: high score is 54718
// 30 players; last marble is worth 5807 points: high score is 37305

// 446 players; last marble is worth 71522 points.
$numPlayers = 446;
$players = array_fill(0, $numPlayers, 0);
$turns = 71522;

$firstMarble = new ListNode(0);
$firstMarble->next = 0;
$firstMarble->prev = 0;
$currentMarble = $firstMarble;
$marbles[0] = $firstMarble;
$numMarbles = 1;
// $scoreForTurn = [];
//$turns * 100
for ($i=1; $i <= $turns * 100; $i++) {
  if ($i % 10000 === 0) {
    echo("Turn $i of $turns\n");
    echo(memory_get_peak_usage() . " mem\n");
  }

  if ($i == 116540) {
    echo("CurrentMarble " . $currentMarble->data . " $numMarbles" . "\n");
    // echo("Next Marble " . $currentMarble->next->data . "\n");
    // echo("Next prev Marble " . $currentMarble->next->prev->data . "\n");
  }
  if ($i % 23 === 0) {
    // Do the special thing.
    for ($ii=0; $ii <= 7; $ii++) {
      $currentMarble = $marbles[$currentMarble->prev];
    }
    $removed = $marbles[$currentMarble->next];
    // Need to fix this.
    $after = $marbles[$removed->next];
    // Skip over the removed one by making current point to after and after point back to current.
    $after->prev = $currentMarble->data;
    $currentMarble->next = $after->data;
    // Then set the current marble to the one after the removed one.
    $currentMarble = $after;
    $numMarbles--;

    unset($removed->next);
    unset($removed->prev);
    unset($marbles[$removed->data]);

    $score = $i + $removed->data;
    // $scoreForTurn[$i] = $score;
    $player = $i % $numPlayers;
    $players[$player] += $score;
    // echo("Player $player scored $score on turn $i\n");

    unset($removed);
  } else {
    // Add in right spot.
    $currentMarble = $marbles[$currentMarble->next];
    $newMarble = new ListNode($i);
    $marbles[$i] = $newMarble;
    if ($i == 116540) {
      echo("Set next on new marble\n");
    }
    $newMarble->next = $currentMarble->next;
    if ($i == 116540) {
      echo("After set next\n");
    }
    $newMarble->prev = $currentMarble->data;

    $marbles[$currentMarble->next]->prev = $newMarble->data;
    $currentMarble->next = $newMarble->data;
    $currentMarble = $newMarble;
    $numMarbles++;
  }

  // Debug print.
  if ($i <= 25) {
    $curMarble = $firstMarble;
    echo("[" . $i % $numPlayers . "] " .$curMarble->data);
    for ($ii=1; $ii < $numMarbles; $ii++) {
      $curMarble = $marbles[$curMarble->next];
      echo("," . $curMarble->data);
    }
    echo(" - " . $currentMarble->data . " $numMarbles" . "\n");
  }

  if ($i == $turns) {
    echo("Part 1 Complete\n");
    arsort($players);
    $part1answer = $players[key($players)];
    echo("Part 1: " . $part1answer . "\n");
  }
}
echo ("Last Marble $i\n");

arsort($players);
// print_r($players);
$answer = $players[key($players)];
echo("Part 2: " . $answer . "\n");

// 4993884974 is too high.
// 3277920293 is right.