#!/bin/sh

killall syskiller
killall cpukiller 
cpu_count=$(cat /proc/cpuinfo  | grep  process  | wc -l )
./syskiller $cpu_count
