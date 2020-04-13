#!/bin/sh

settings_path=/conf/watchguard-settings.txt

temp=$(/conf/WGXepc -t | tail -1)
speed=100

auto=$(echo $(grep AUTO $settings_path) | cut -d ":" -f2)

force_speed=$(echo $(grep FAN_SPEED $settings_path) | cut -d ":" -f2)

t1=$(echo $(grep TEMP_MAX_1 $settings_path) | cut -d ":" -f2)
t2=$(echo $(grep TEMP_MAX_2 $settings_path) | cut -d ":" -f2)
t3=$(echo $(grep TEMP_MAX_3 $settings_path) | cut -d ":" -f2)

f1=$(echo $(grep FAN_1 $settings_path) | cut -d ":" -f2)
f2=$(echo $(grep FAN_2 $settings_path) | cut -d ":" -f2)
f3=$(echo $(grep FAN_3 $settings_path) | cut -d ":" -f2)

if [ $auto -eq 1 ]
then
	if [ $temp -ge $t3 ]
	then
		speed=100
	elif [ $temp -gt $t2 ]
	then
		speed=$f3
	elif [ $temp -gt $t1 ]
	then
		speed=$f2
	else
		speed=$f1
	fi
else
	speed=$force_speed
fi

echo $speed

speed=$(( $(($speed*255)) / 100 ))

hex=$(printf "%x\n" $speed)

/conf/WGXepc -f $hex