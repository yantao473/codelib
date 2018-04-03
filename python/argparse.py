#!/usr/bin/env python
# -*- coding: utf-8 -*-


import argparse


def getArgs():
    parser = argparse.ArgumentParser()
    parser.add_argument('-q', '--queue', dest='queue', help='queue name', required=True)
    parser.add_argument('--ip', dest='ip', help='ip', required=True)
    parser.add_argument('--mhost', dest='mysql_host', help='mysql connect ip', default='m3307.sae.sina.com.cn')
    parser.add_argument('--mport', dest='mysql_port', help='mysql connect port', type=int, default=3307)
    parser.add_argument('--user', dest='user', help='mysql connect user', default='sae_devops')
    parser.add_argument('--passwd', dest='passwd', help='mysql connect password', default='GBROmH1ulLg5LIbu')
    parser.add_argument('--rhost', dest='redis_host', help='redis connect ip', default='redis.devops.sae.sina.com.cn')
    parser.add_argument('--rport', dest='redis_port', help='redis connect port', type=int, default=6395)
    parser.add_argument('--bwlimit', dest='bwlimit', help='rsync band width limit (KB)', type=int, default=75000)
    parser.add_argument('--rsyncbin', dest='rsyncbin', help='rsync bin', default="/usr/local/sae/rsync/bin/rsync")
    args = parser.parse_args()
    return vars(args)
