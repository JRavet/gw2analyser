#!/bin/bash

if [[ $(screen -ls | grep 'NAt[0-4]') ]]; then # determine if any NA collectors are running
	screen -ls | grep 'NAt[0-4]' | cut -d. -f1 | awk '{print $1}' | xargs kill # kills all screens for NA
fi

if [[ $1 != '-k' ]]; then # if the user wanted to just kill all screens, don't open new ones
	screen -dmS NAt1 php active_collector.php "1-1"
	screen -dmS NAt2 php active_collector.php "1-2"
	screen -dmS NAt3 php active_collector.php "1-3"
	screen -dmS NAt4 php active_collector.php "1-4"
fi

if [[ $(screen -ls | grep 'EUt[0-5]') ]]; then # determine if any EU collectors are running
	screen -ls | grep 'EUt[0-5]' | cut -d. -f1 | awk '{print $1}' | xargs kill # kills all screens for EU
fi

if [[ $1 != '-k' ]]; then # if the user wanted to just kill all screens, don't open new ones
	screen -dmS EUt1 php active_collector.php "2-1"
	screen -dmS EUt2 php active_collector.php "2-2"
	screen -dmS EUt3 php active_collector.php "2-3"
	screen -dmS EUt4 php active_collector.php "2-4"
	screen -dmS EUt5 php active_collector.php "2-5"
fi