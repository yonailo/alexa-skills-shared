<?php
header('Content-type: application/rss+xml; charset=utf-8');

$feedName = "COVID-19 data";
$feedDesc = "COVID-19 data from data.gouv.fr";
$feedURL = "https://www.tuxe.es/alexa-rss-covid/";
$feedBaseURL = "https://data.gouv.fr/"; // must end in trailing forward slash (/).

?><<?= '?'; ?>xml version="1.0" encoding="UTF-8" <?= '?'; ?>>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<ttl>30</ttl>
		<title><?=$feedName?></title>
		<link><?=$feedURL?></link>
		<description><?=$feedDesc?></description>
		<atom:link href="<?=$feedURL ?>" rel="self" type="application/rss+xml" />
<?php

########### dynamic data #####################

$json = file_get_contents("./covid.json");
$data = (array)json_decode($json);

########### dynamic data #####################

setlocale(LC_TIME, 'fr_FR.UTF-8');

$aux = [];
$aux['title'] = "H: " . sprintf("%+d", $data['hosp']) . ", R: " . sprintf("%+d",$data['rea']) . ", D: " . sprintf("%+d",$data['dc']);
$aux['title'] .= ", TH: " . $data['total_hosp'] . ", TR: " . $data['total_rea'] . ", TD: " . $data['total_dc'];
$aux['title'] .= ", IH: " . $data['incid_hosp'] . ", IR: " . $data['incid_rea'] . ", ID: " . $data['incid_dc'];

$aux['desc'] = "Données hospitalières COVID-19 en France au " . strftime('%d %B %Y', $data['timestamp']) . '.';

if($data['hosp'] > 0) {
  $x = "augmenté";
  $aux['desc'] .= " Le nombre d'hospitalisations a $x de " . abs($data['hosp']) . " cas.";
}
elseif($data['hosp'] < 0) {
  $x = "diminué";
  $aux['desc'] .= " Le nombre d'hospitalisations a $x de " . abs($data['hosp']) . " cas.";
}
else {
  $aux['desc'] .= " Il n'y a pas eu de nouvelles hospitalisations. Bonne nouvelle !.";
}
$aux['desc'] .= " Le nombre total de personnes hospitalisées est de " . $data['total_hosp'] . ".";


if($data['rea'] > 0) {
  $x = "augmenté";
  $aux['desc'] .= " Les soins intensif ont $x de " . abs($data['rea']) . " cas.";
}
elseif($data['rea'] < 0) {
  $x = "diminué";
  $aux['desc'] .= " Les soins intensif ont $x de " . abs($data['rea']) . " cas.";
}
else {
  $aux['desc'] .= " Il n'y a pas eu de nouveaux cas en soin intensifs. Bonne nouvelle !.";
}
$aux['desc'] .= " Le nombre total de personnes en soins intensif est de " . $data['total_rea'] . ".";

if($data['dc'] == 0) {
  $aux['desc'] .= " Il n'y a pas eu de nouveaux décès. Bonne nouvelle !.";
}
else {
  $aux['desc'] .= " Il y a eu " . abs($data['dc']) . " nouveaux décès.";
}
$aux['desc'] .= " Le nombre total de personnes décédées est actuellement de " . $data['total_dc'] . ".";

$aux['desc'] .= " Par rapport aux incidences hospitalières, en France, aujourd'hui, il y a eu " . $data['incid_hosp'] . " nouvelles hospitalisations.";
$aux['desc'] .= " " . $data['incid_rea'] . " nouvelles personnes ont été admises en soins intensifs,";
$aux['desc'] .= "et on déplore aujourd'hui " . $data['incid_dc'] . " personnes décédées.";

$aux['timestamp'] = time();


$output = [];
$output[] = $aux;

for($i=0; $i< count($output); $i++) {
  echo "\t\t<item>\n";
  echo "\t\t\t<title>". $output[$i]['title'] ."</title>\n";
  echo "\t\t\t<link>". $feedBaseURL ."</link>\n";
  echo "\t\t\t<description>". $output[$i]['desc'] ."</description>\n";
  echo "\t\t\t<guid>rn:uuid:b385254e-dc31-4c4a-afc5-".substr(md5($output[$i]['title']), 0, 12)."</guid>\n";
  echo "\t\t\t<pubDate>". date("Y-m-d\Th:i:s.0\Z", $output[$i]['timestamp']) ."</pubDate>\n";
  echo "\t\t</item>\n";
}
?>
	</channel>
</rss>
