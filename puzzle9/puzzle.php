<?
class ListNode {
  /* Data to hold */
  public $data;
  /* Link to next node */
  public $next = NULL;
  /* Link to prev node */
  public $prev = NULL;

  /* Node constructor */
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

$scoreForTurn = [];
$currentMarble = new ListNode(0);
$firstMarble = $currentMarble;
// $currentMarble->next = &$currentMarble;
for ($i=1; $i <= 5; $i++) {
  $newMarble = new ListNode($i);
  $currentMarble->next = $newMarble;
  $currentMarble = $newMarble;
  print_r($firstMarble);
}


// TODO array_splice is too slow for a large number of turns.
// A linked list would work much better here for adding and removing.
$firstMarble = new ListNode(0);
$firstMarble->next = $firstMarble;
$firstMarble->prev = $firstMarble;
$currentMarble = $firstMarble;
$numMarbles = 1;
for ($i=1; $i <= $turns * 100; $i++) {
  if ($i % 23 === 0) {
    // Do the special thing.
    for ($ii=0; $ii <= 7; $ii++) {
      $currentMarble = $currentMarble->prev;
    }
    $removed = $currentMarble->next;
    $currentMarble->next->next->prev = $currentMarble;
    $currentMarble->next = $currentMarble->next->next;
    $currentMarble = $currentMarble->next;
    $numMarbles--;

    unset($removed->next);
    unset($removed->prev);
    $score = $i + $removed->data;
    $scoreForTurn[$i] = $score;
    $player = $i % $numPlayers;
    $players[$player] += $score;
    // echo("Player $player scored $score on turn $i\n");
  } else {
    // Add in right spot.
    $currentMarble = $currentMarble->next;
    $newMarble = new ListNode($i);
    $newMarble->prev = $currentMarble;
    $newMarble->next = $currentMarble->next;

    if ($currentMarble->next) {
      $currentMarble->next->prev = $newMarble;
    }
    $currentMarble->next = $newMarble;
    $currentMarble = $newMarble;
    $numMarbles++;
  }
  if ($i % 10000 === 0) {
    echo("Turn $i of $turns\n");
    echo(memory_get_usage() . " mem\n");
  }

  // Debug print.
  // $curMarble = $firstMarble;
  // echo($curMarble->data);
  // for ($ii=1; $ii < $numMarbles; $ii++) {
  //   $curMarble = $curMarble->next;
  //   echo("," . $curMarble->data);
  // }
  // echo(" - " . $currentMarble->data . " $numMarbles" . "\n");

  if ($i == $turns) {
    echo("Part 1 Complete\n");
    arsort($players);
    $part1answer = $players[key($players)];
    echo("Part 1: " . $part1answer . "\n");
  }
}

arsort($players);
$answer = $players[key($players)];
echo("Part 2: " . $answer . "\n");
