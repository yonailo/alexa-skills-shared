<?php

$filepath="/var/log/asterisk/sms.txt";

$data = file_get_contents($filepath);

$arr = explode("\n", $data);

setlocale(LC_ALL, 'fr_FR');

$pattern = "/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) - dongle0 - \+33(\d{09}): (.*)$/";
$output = [];

foreach($arr as $sms) {
  if(preg_match($pattern, $sms, $matches)) {
    $date = $matches[1];
    $tel = $matches[2];
    $text = $matches[3];

    if(! strstr($text, 'http') && !empty($text) && ! stristr($text, 'code') && mb_check_encoding($text)) {
      $aux = [];
      $aux['date'] = strtotime($date);
      $aux['tel'] = '0'.$tel;
      $aux['text'] = $text;

      $output[] = $aux;
    }
  }
}


if(!empty($output)) {
  $output = array_reverse($output);
  file_put_contents('./sms.json', json_encode($output));
  exit(0);
}
else {
  exit(1);
}

