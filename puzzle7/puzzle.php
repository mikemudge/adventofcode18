<?

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . "input";
$data = file_get_contents($filename);
$lines = explode("\n", $data);
foreach($lines as $i=>$line) {
  $pieces = split(" ", $line);
  $step = $pieces[1];
  $dependantStep = $pieces[7];
  if (!isset($graph[$step])) {
    $graph[$step] = [];
  }
  if (!isset($graph[$dependantStep])) {
    $graph[$dependantStep] = [];
  }

  $graph[$step]['children'][] = $dependantStep;
  $graph[$dependantStep]['parents'][] = $step;
}

$start=[];
print_r($graph);
foreach ($graph as $key => $node) {
  if (!$node['parents']) {
    $start[] = $key;
  }
}
$available = array_merge($start);
$value = [];
while(!empty($available)) {
  rsort($available);
  $next = array_pop($available);
  $value[] = $next;
  echo(join("", $value) . " added $next\n");
  $children = $graph[$next]['children'];
  if (!$children) {
    $children = [];
  }
  foreach($children as $v) {
    // Look at the parents for value and see if they are all used.
    $ready = True;
    foreach($graph[$v]['parents'] as $dStep) {
      if (!in_array($dStep, $value)) {
        $ready = False;
        break;
      }
    }
    if ($ready) {
      echo "$next freed up $v\n";
      $available[] = $v;
    }
  }
}

echo("Part 1: " . join("", $value) . "\n");

$available = array_merge($start);
$workTimes = array_flip(range('A', 'Z'));
$time = 0;
$workersFreeAt = [0, 0, 0, 0, 0];
$value = [];
while(count($value) < 26) {
  $completed = $addAt[$time];
  if (!$completed) {
    $completed = [];
  }
  foreach ($completed as $next) {
    $value[] = $next;
    echo("Time $time: " . join("", $value) . " added $next\n");
    $children = $graph[$next]['children'];
    if (!$children) {
      $children = [];
    }
    foreach($children as $v) {
      // Look at the parents for value and see if they are all used.
      $ready = True;
      foreach($graph[$v]['parents'] as $dStep) {
        if (!in_array($dStep, $value)) {
          $ready = False;
          break;
        }
      }
      if ($ready) {
        echo "$next freed up $v\n";
        $available[] = $v;
      }
    }
  }

  rsort($available);

  for ($i=0; $i < 5; $i++) {
    if ($workersFreeAt[$i] <= $time) {
      $next = array_pop($available);
      if (!$next) {
        break;
      }
      echo ("Worker $i starting on $next\n");
      $completeTime = $time + 61 + $workTimes[$next];
      $workersFreeAt[$i] = $completeTime;
      $addAt[$completeTime][] = $next;
    }
  }
  $time++;
}

echo("Part 2: " . join("", $value) . "\n");
