<?php
$fh = fopen(__DIR__ . '/2008-2012.csv', 'r');
$header = false;
$result = array();
while($line = fgetcsv($fh, 2048)) {
  if(count($line) === 57) {
    if(false === $header) {
      $header = $line;
    } else {
      $ym = explode('年', $line[0]);
      $ym[0] = intval($ym[0]) + 1911;
      $ym[1] = str_pad(intval($ym[1]), 2, '0', STR_PAD_LEFT);
      $ym = getYmKey($ym);

      for($i = 1; $i <= 28; $i++) {
        if(false === strpos($header[$i], '改制前')) {
          $city = mb_substr($header[$i], 0, 3, 'utf-8');
          if(!isset($result[$city])) {
            $result[$city] = array();
          }
          if(!isset($result[$city][$ym])) {
            $result[$city][$ym] = array(0, 0,);
          }
          $result[$city][$ym][0] = getNum($line[$i]);
        }
      }
      for($i = 29; $i <= 56; $i++) {
        if(false === strpos($header[$i], '改制前')) {
          $city = mb_substr($header[$i], 0, 3, 'utf-8');
          $result[$city][$ym][1] += getNum($line[$i]);
        }
      }
    }
  }
}

$header = false;
$fh = fopen(__DIR__ . '/2003-2007.csv', 'r');
while($line = fgetcsv($fh, 2048)) {
  if(count($line) === 45) {
    if(false === $header) {
      $header = $line;
    } else {
      $ym = explode('年', $line[0]);
      $ym[0] = intval($ym[0]) + 1911;
      $ym[1] = str_pad(intval($ym[1]), 2, '0', STR_PAD_LEFT);
      if($ym[1] === '00') {
        continue;
      }
      $ym = getYmKey($ym);
      for($i = 1; $i <= 22; $i++) {
        $city = mb_substr($header[$i], 0, 3, 'utf-8');
        if(!isset($result[$city][$ym])) {
          $result[$city][$ym] = array(0, 0,);
        }
        $result[$city][$ym][0] = getNum($line[$i]);
      }
      for($i = 23; $i <= 44; $i++) {
        $city = mb_substr($header[$i], 0, 3, 'utf-8');
        $result[$city][$ym][1] += getNum($line[$i]);
      }
    }
  }
}

$header = false;
$fh = fopen(__DIR__ . '/2013-2017.csv', 'r');
while($line = fgetcsv($fh, 2048)) {
  if(count($line) === 45) {
    if(false === $header) {
      $header = $line;
    } else {
      $ym = explode('年', $line[0]);
      $ym[0] = intval($ym[0]) + 1911;
      $ym[1] = str_pad(intval($ym[1]), 2, '0', STR_PAD_LEFT);
      $ym = getYmKey($ym);
      for($i = 1; $i <= 22; $i++) {
        $city = mb_substr($header[$i], 0, 3, 'utf-8');
        if(!isset($result[$city][$ym])) {
          $result[$city][$ym] = array(0, 0,);
        }
        $result[$city][$ym][0] = getNum($line[$i]);
      }
      for($i = 23; $i <= 44; $i++) {
        $city = mb_substr($header[$i], 0, 3, 'utf-8');
        $result[$city][$ym][1] += getNum($line[$i]);
      }
    }
  }
}

$fh = fopen(__DIR__ . '/1998-2002.csv', 'r');
$loopCount = 0;
while($line = fgetcsv($fh, 2048)) {
  $parts = explode('年/ ', $line[0]);
  if(count($parts) === 2) {
    $ym = $parts[0] + 1911;
    $city = mb_substr($parts[1], 0, 3, 'utf-8');
    if($city === '新北市') {
      if($loopCount === 0) {
        $loopCount = 1;
      } else {
        $loopCount = 0;
      }
    }
    if(!isset($result[$city])) {
      $result[$city] = array();
    }
    if(!isset($result[$city][$ym])) {
      $result[$city][$ym] = array();
    }
    if($loopCount === 1) {
      $result[$city][$ym][0] = getNum($line[1]);
    } else {
      $result[$city][$ym][1] = getNum($line[1]);
    }
  }
}

// $header = false;
// $fh = fopen(__DIR__ . '/2018.csv', 'r');
// while($line = fgetcsv($fh, 2048)) {
//   if(count($line) === 45) {
//     if(false === $header) {
//       $header = $line;
//     } else {
//       $ym = explode('年', $line[0]);
//       $ym[0] = intval($ym[0]) + 1911;
//       $ym[1] = str_pad(intval($ym[1]), 2, '0', STR_PAD_LEFT);
//       $ym = getYmKey($ym);
//       for($i = 1; $i <= 22; $i++) {
//         $city = mb_substr($header[$i], 0, 3, 'utf-8');
//         if(!isset($result[$city][$ym])) {
//           $result[$city][$ym] = array(0, 0,);
//         }
//         $result[$city][$ym][0] = getNum($line[$i]);
//       }
//       for($i = 23; $i <= 44; $i++) {
//         $city = mb_substr($header[$i], 0, 3, 'utf-8');
//         $result[$city][$ym][1] += getNum($line[$i]);
//       }
//     }
//   }
// }

function getYmKey($ym) {
  return $ym[0];
  switch($ym[1]) {
    case '01':
    case '02':
    case '03':
    case '04':
    case '05':
    case '06':
    return $ym[0] . '06';
    break;
    default:
    return $ym[0] . '12';
  }
}

function getNum($n) {
  return intval(preg_replace('/[^\d.]/', '', $n));
}

$chartData1 = $chartData2 = $chartData3 = array(
  'labels' => array(),
  'datasets' => array(),
);
$labelDone = false;
foreach($result AS $city => $data) {
  ksort($data);
  if(false === $labelDone) {
    $chartData1['labels'] = $chartData2['labels'] = $chartData3['labels'] = array_keys($data);
    $labelDone = true;
  }

  $dataset1 = $dataset2 = $dataset3 = array(
    'label' => $city,
    'data' => array(),
  );
  foreach($data AS $k => $v) {
    $dataset1['data'][] = $v[0];
    $dataset2['data'][] = $v[1];
    if(empty($v[0])) {
      $dataset3['data'][] = 0;
    } else {
      $dataset3['data'][] = round($v[1] / $v[0]);
    }
  }
  $chartData1['datasets'][] = $dataset1;
  $chartData2['datasets'][] = $dataset2;
  $chartData3['datasets'][] = $dataset3;
}

file_put_contents(__DIR__ . '/data1.json', json_encode($chartData1));
file_put_contents(__DIR__ . '/data2.json', json_encode($chartData2));
file_put_contents(__DIR__ . '/data3.json', json_encode($chartData3));
