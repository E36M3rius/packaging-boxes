<?php

/**
 * <****> packing algorithm
 * I will use simple scripting techniques for now however an
 * Object Oriented approach is recommended instead
 *
 * @author Marius Iordache
 */

// initialize variables
$productSizes = array(
  45 => 500,
  34 => 500,
  41 => 1500,
  35 => 1500,
  33 => 2500,
  40 => 12000
);

// test
#$productSizes = array_reverse($productSizes, true);

$initialOrder = $order = array(
  40 => 1,
  33 => 4,
  35 => 3,
  41 => 1,
  34 => 3,
  45 => 1
);

$maxBoxSize = 15000;
$boxes = array();
$totalItems = array_sum($order);
$boxIndex = 1;
$boxVolumeMeter = 0;

// we assume that product size will never be bigger than 15000 cm3
// loop over the items in order to start packaging process
for($i = 1; $i <= $totalItems; $i++) {
  // go over the sizes and fill in from smallest to biggest
  foreach($productSizes as $productId => $productSize) {
    // try to group
    for($j=1; $j <= $initialOrder[$productId]; $j++) {
      if (isset($order[$productId]) && $order[$productId] > 0) {
        if ($productSize + $boxVolumeMeter > 15000) {
          // need new box
          $boxVolumeMeter = $productSize;
          $boxIndex += 1;
        } else {
          // add to the box
          $boxVolumeMeter += $productSize;
        }

        // adjust the counters
        if (!isset($boxes["box_".$boxIndex][$productId])) {
          $boxes["box_".$boxIndex][$productId] = 0;
        }

        $boxes["box_".$boxIndex][$productId] += 1;
        $order[$productId] -= 1;
      }
    }
  }
}

if (array_sum($order) != 0) {
  echo "We have a problem, the order was not fully packaged. \n";
  print_r($order);
}

// print the packaging
echo "\n\n Packaging contents: \n"
foreach($boxes as $boxName => $boxContents) {
  echo $boxName."\n";
  echo "Box size: ".computeBoxVolume($boxContents, $productSizes)." cm3 \n";
  echo "Box items:\n";
  print_r($boxContents);
  echo "\n\n";
}

/**
 * Given an input of 2 arrays, it calculates the volume in cm3 of a box
 * @param  array $contents
 * @param  array $productSizes
 * @return int volume
 */
function computeBoxVolume(array $contents, array $productSizes)
{
  $volume = 0;

  foreach($contents as $productId => $quantity) {
    if (isset($productSizes[$productId])) {
      $volume += $productSizes[$productId] * $quantity;
    }
  }

  return $volume;
}
