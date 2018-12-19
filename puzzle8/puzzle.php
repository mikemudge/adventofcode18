<?
function parseNode(&$values) {
  $numChildren = array_shift($values);
  $numMetaItems = array_shift($values);
  for ($i=0; $i < $numChildren; $i++) {
    $node['children'][] = parseNode($values);
  }
  for ($i=0; $i < $numMetaItems; $i++) {
    $v = array_shift($values);
    if (!$v) {
      throw new Exception('No values left');
    }
    $node['meta'][] = intval($v);
  }

  return $node;
}

function iterateTree($node, $func) {
  $func($node);
  $children = $node['children'];
  if (!$children) {
    $children = [];
  }
  foreach($children as $child) {
    iterateTree($child, $func);
  }
}

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$data = file_get_contents($filename);
$lines = explode("\n", $data);
$values = split(" ", $lines[0]);

$root = parseNode($values);
$answer = 0;
iterateTree($root, function($node) {
  global $answer;
  $meta = array_sum($node['meta']);
  echo(join(",", $node['meta']) . " = " . $meta ."\n");
  $answer += $meta;
});

echo("Part 1: " . $answer . "\n");

function calculateValue($node) {
  if ($node['value']) {
    return $node['value'];
  }
  $children = $node['children'];
  if (!$children) {
    $children = [];
  }
  $numChildren = count($children);
  if ($numChildren < 1) {
    $value = array_sum($node['meta']);
    echo("Node value meta summed as $value\n");
    $node['value'] = $value;
    return $value;
  }
  $value = 0;
  foreach($node['meta'] as $meta) {
    $child = $node['children'][$meta - 1];
    if ($child) {
      $value += calculateValue($child);
    }
  }
  // Cache it so we don't need to recalculate it.
  echo("Node value calculated as $value - $numChildren children " . join(",", $node['meta']) . "\n");
  $node['value'] = $value;
  return $value;
}

$answer = calculateValue($root);
echo("Part 2: " . $answer . "\n");
