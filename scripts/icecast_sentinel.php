<?php

$sentinel = '/********/sentinel.txt';
$stats_file = 'http://******:******@icecast.tuxe.es:8000/admin/stats';

while(true) {
  $l_example = 0;
  $l_stream = 0;
  echo "...checking listeners...\n";

  $stats = file_get_contents($stats_file);
  $stats = simplexml_load_string($stats);

  foreach($stats->xpath('source') as $source) {
    if(($source['mount'] == '/example.mp3') || ($source['mount'] == '/example_ktotv.mp3')) {
      $l_example += (int)$source->listeners;
      if($source->listeners > 0) {
        echo "Listeners waiting on example, create sentinel for ffmpeg\n";
        touch($sentinel);
      }
    }

    if(($source['mount'] == '/tve1.mp3') || ($source['mount'] == '/ktotv.mp3')) {
      $l_stream += (int)$source->listeners;
      if($source->listeners > 0) {
        echo "Listeners waiting on tve1-ktotv, create sentinel for ffmpeg\n";
        touch($sentinel);
      }
    }
  }

  if((!$l_example) && (!$l_stream)) {
    echo "No more listeners, removing sentinel\n";
    @unlink($sentinel);
    exec('/usr/bin/killall -9 ffmpeg_alexa 2>/dev/null');
  }

  // 5 secs checking
  sleep(5);
}
