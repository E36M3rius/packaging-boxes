<?php

/**
 * <****> packing algorithm
 * I will use simple scripting techniques for now however an
 * Object Oriented approach is recommended instead
 *
 * @author Marius Iordache
 */

/**
 * 1 = produces boxes with contents more balanced accross the boxes
 * 2 = tries to maximize the space usage however last box can be almost empty
 * @var integer
 */
$method = 1;

// initialize variables
$productSizes = array(
  45 => 500,
  34 => 500,
  41 => 1500,
  35 => 1500,
  33 => 2500,
  40 => 12000
);

$order = array(
  40 => 1,
  33 => 4,
  35 => 3,
  41 => 1,
  34 => 3,
  45 => 1
);

// check method and sort. This will greatly effect the packaging.
if ($method === 1) {
  asort($productSizes);
} else if($method === 2) {
  arsort($productSizes);
}

// run
runPackagingAlgo($order, $productSizes);

function runPackagingAlgo($order, $productSizes) {
  // initialize variables
  $boxMaxSize = 15000;
  $boxes = array();
  $totalItems = array_sum($order);
  $boxIndex = 1;
  $boxVolumeMeter = 0;
  $initialOrder = $order;

  // we assume that product size will never be bigger than 15000 cm3
  // loop over the items in order to start packaging process
  for($i = 1; $i <= $totalItems; $i++) {
    // go over the sizes and fill in from smallest to biggest
    foreach($productSizes as $productId => $productSize) {
      // we make sure to have a valid product inside the order
      if (isset($order[$productId])) {
        // try to group
        for($j=1; $j <= $initialOrder[$productId]; $j++) {
          if ($order[$productId] == 0) {
            continue; // done with this product
          }

          if ($productSize + $boxVolumeMeter > $boxMaxSize) {
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
        } // group loop
      } // check if product is in order
    } // product size loop
  } // items loop

  if (array_sum($order) != 0) {
    echo "We have a problem, the order was not fully packaged. \n";
    print_r($order);
  }

  // print the packaging
  echo "\n\n Packaging contents: \n";
  foreach($boxes as $boxName => $boxContents) {
    echo "Label: $boxName \n";
    echo "Box Items: ".array_sum($boxContents)." \n";
    echo "Box size: ".computeBoxVolume($boxContents, $productSizes)." cm3 \n";
    echo "Box contents:\n";
    print_r($boxContents);
    echo "\n\n";
  }
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
