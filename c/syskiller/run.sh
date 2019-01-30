#!/bin/sh

killall syskiller
cpu_count=$(cat /proc/cpuinfo  | grep  process  | wc -l )
./syskiller $cpu_count
