#!/usr/bin/env python
# -*- coding: utf-8 -*-

import argparse
import datetime
import MySQLdb

import sys

from redis import Redis


def handle_data(domain, vtuple, slist, handle_index):
    """ 默认取前100条数据 """
    list_len = len(slist)

    if slist:
        for i in range(list_len):
            tmp_tuple = slist[i]
            if vtuple[handle_index] > tmp_tuple[handle_index + 1]:
                slist.insert(i, (domain,) + vtuple)
                break
        else:
            slist.append((domain,) + vtuple)
    else:
        slist.append((domain,) + vtuple)

    return slist[:100]


def getArgs():
    parser = argparse.ArgumentParser(
        description='listen local port and connect redis')
    parser.add_argument('mhost', help='mysql host')
    parser.add_argument('mport', type=int, help='mysql port')
    parser.add_argument('-d', '--days', type=int, default=1, help='mysql port')
    parser.add_argument('--rhost', default='localhost', help='redis server')
    parser.add_argument('--rport', type=int, default=6379, help='redis port')
    args = parser.parse_args()
    return vars(args)


def write2db(mhost, port, dlist, rtype, index):
    pass


if __name__ == '__main__':
    args = getArgs()
    yesterday = datetime.datetime.now() - datetime.timedelta(days=args.get('days'))
    yesterday_str = yesterday.strftime('%Y-%m-%d')
    yesterday_key = '*_' + yesterday_str

    vsort = []
    csort = []
    rsort = []
    bsort = []

    redis = Redis(host=args.get('rhost'), port=args.get('rport'), db=0)
    keylist = redis.keys(yesterday_key)
    for k in keylist:
        v = int(redis.hget(k, 'v'))
        c = int(redis.hget(k, 'c'))
        r = int(redis.hget(k, 'r'))
        b = int(redis.hget(k, 'b'))

        metatumple = (v, c, r, b)
        vsort = handle_data(k, metatumple, vsort, 0)
        csort = handle_data(k, metatumple, csort, 1)
        rsort = handle_data(k, metatumple, rsort, 2)
        bsort = handle_data(k, metatumple, bsort, 3)

    write2db(args.get('mhost'), args.get('mport'), vsort, 'visits', 1)
    write2db(args.get('mhost'), args.get('mport'), csort, 'content_length', 2)
    write2db(args.get('mhost'), args.get('mport'), rsort, 'request_length', 3)
    write2db(args.get('mhost'), args.get('mport'), bsort, 'body_bytes_sent', 4)
