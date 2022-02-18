#!/bin/bash

#set -exuo pipefail

LIVESTREAM_URL=$1
LIVESTREAM_OUTPUT=$2
SENTINEL="***********/sentinel.txt"

if [[ ! $# -eq 2 ]]; then
  echo "usage: $0 <URL> <DESTINATION>"
  exit 1
fi

# Referencing FFMpeg to Icecast2 Streaming Sample Gist: https://gist.github.com/keiya/c8a5cbd4fe2594ddbb3390d9cf7dcac9

URL="$LIVESTREAM_URL"
channels=2

samplerate=44100
bitrate="256k"
codec="libmp3lame"

/usr/bin/ffmpeg -nostdin -i $URL -ab $bitrate -ar $samplerate -ac $channels -c:a $codec -f mp3 -content_type 'audio/mpeg' $LIVESTREAM_OUTPUT
