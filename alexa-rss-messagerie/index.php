<?php
header('Content-type: application/rss+xml; charset=utf-8');

$feedName = "Messagerie des inconnus";
$feedDesc = "Messagerie des inconnus";
$feedURL = "https://www.tuxe.es/alexa-rss-messagerie/";
$feedBaseURL = "https://github.com/yonailo/alexa-skills-shared/"; // must end in trailing forward slash (/).

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

$json = file_get_contents("./sms.json");
$data = (array)json_decode($json);

########### dynamic data #####################

setlocale(LC_TIME, 'fr_FR.UTF-8');
$output = [];

foreach($data as $sms) {
  $tel = '';
  for($i = 0; $i < 10; $i++) {
    if($i != 0 && $i % 2 == 0) {
      $tel .= ', '. $sms->tel[$i];
    }
    else {
      $tel .= $sms->tel[$i];
    }
  }
  $tel .= '.';

  $aux = [];
  $aux['title'] = 'Tel : ' . $sms->tel . ', le ' . date('d/m/Y H:i:s', $sms->date);
  $aux['desc'] = "Message reçu du numéro ". $tel . " Le " . strftime('%e %B %Y', $sms->date) . ' à ' . date('H', $sms->date) . ' heures ' . date('i', $sms->date) . '. ';
  $aux['desc'] .= $sms->text;
  $aux['timestamp'] = $sms->date;

  $output[] = $aux;
}



for($i=0; $i< count($output); $i++) {
  echo "\t\t<item>\n";
  echo "\t\t\t<title>". $output[$i]['title'] ."</title>\n";
  echo "\t\t\t<link>". $feedBaseURL ."</link>\n";
  echo "\t\t\t<description>". $output[$i]['desc'] ."</description>\n";
  echo "\t\t\t<guid>rn:uuid:b385254e-dc31-4c4a-afc5-".substr(md5($output[$i]['timestamp']), 0, 12)."</guid>\n";
  echo "\t\t\t<pubDate>". date("Y-m-d\Th:i:s.0\Z", $output[$i]['timestamp']) ."</pubDate>\n";
  echo "\t\t</item>\n";
}
?>
	</channel>
</rss>
