<?php

$url = "https://www.data.gouv.fr/fr/datasets/r/63352e38-d353-4b54-bfd1-f1b3ee1cabd7";

$today = date('Y-m-d');
$yesterday = date('Y-m-d', time() - 86400);

$data = [];
$data[$yesterday] = ['hosp' => 0, 'rea' => 0, 'dc' => 0];
$data[$today] = ['hosp' => 0, 'rea' => 0, 'dc' => 0];
$yesterday_found = FALSE;
$today_found = FALSE;

$h = fopen($url, 'r');
while (($line = fgetcsv($h, 0, ';', '"')) !== FALSE) {
  list($dep, $sex, $date, $hosp, $rea, $rad, $dc) = $line;

  if($date == $yesterday) {
    if($sex == 0) {
      $data[$yesterday]['hosp'] += $hosp;
      $data[$yesterday]['rea'] += $rea;
      $data[$yesterday]['dc'] += $dc;
      $yesterday_found = TRUE;
    }
  }

  if($date == $today) {
    if($sex == 0) {
      $data[$today]['hosp'] += $hosp;
      $data[$today]['rea'] += $rea;
      $data[$today]['dc'] += $dc;
      $today_found = TRUE;
    }
  }
}
fclose($h);

$output = [];
$output['timestamp'] = time();
$output['hosp'] = $data[$today]['hosp'] - $data[$yesterday]['hosp'];
$output['rea'] = $data[$today]['rea'] - $data[$yesterday]['rea'];
$output['dc'] = $data[$today]['dc'] - $data[$yesterday]['dc'];

$output['total_hosp'] = $data[$today]['hosp'];
$output['total_rea'] = $data[$today]['rea'];
$output['total_dc'] = $data[$today]['dc'];


$nouveaux_url = "https://www.data.gouv.fr/fr/datasets/r/6fadff46-9efd-4c53-942a-54aca783c30c";

$total_incid_hosp = 0;
$total_incid_rea = 0;
$total_incid_dc = 0;

$h = fopen($nouveaux_url, 'r');
while (($line = fgetcsv($h, 0, ';', '"')) !== FALSE) {
  list($dep, $date, $incid_hosp, $incid_rea, $incid_dc, $incid_rad) = $line;

  if($date == $today) {
    $total_incid_hosp += $incid_hosp;
    $total_incid_rea += $incid_rea;
    $total_incid_dc += $incid_dc;
  }
}
fclose($h);

$output['incid_hosp'] = $total_incid_hosp;
$output['incid_rea'] = $total_incid_rea;
$output['incid_dc'] = $total_incid_dc;

$output = json_encode($output);

if($yesterday_found && $today_found) {
  file_put_contents('./covid.json', $output);
  exit(0);
}
else {
  exit(1);
}

