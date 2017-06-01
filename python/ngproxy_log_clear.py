#!/usr/bin/env python
# -*- coding: utf-8 -*-

import argparse
import datetime

from redis import Redis


def getArgs():
    parser = argparse.ArgumentParser(
        description='listen local port and connect redis')
    parser.add_argument('-d', '--days', type=int,
                        default=3, help='reversed days')
    parser.add_argument('-t', '--timerange', type=int,
                        default=7, help='delete time range days')
    parser.add_argument(
        '-r', '--rhost', default='localhost', help='redis server')
    parser.add_argument('-p', '--rport', type=int,
                        default=6379, help='redis port')
    args = parser.parse_args()
    return vars(args)


if __name__ == '__main__':
    args = getArgs()
    key_suffix_list = []
    now = datetime.datetime.now()

    rd = args.get('days')
    time_range = args.get('timerange')

    for d in range(time_range):
        day = now - datetime.timedelta(days=(d + rd))
        day_str = day.strftime('%Y-%m-%d')
        key_suffix_list.append(day_str)

    print key_suffix_list
    redis = Redis(host=args.get('rhost'), port=args.get('rport'), db=0)

    for k in key_suffix_list:
        print 'deleting date: ' + k
        keys = redis.keys('*' + k)
        try:
            redis.delete(*keys)
        except:
            pass
