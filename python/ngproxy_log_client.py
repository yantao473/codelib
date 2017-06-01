#!/usr/bin/env python2.7
# -*- coding: utf-8 -*-

import argparse
import json
import logging
import socket
import sys
import time

from datetime import datetime


def getArgs():
    parser = argparse.ArgumentParser(
        description='connect log server ip and port')
    parser.add_argument('server', help='ip of log server')
    parser.add_argument('port', type=int, help='port of log port')
    parser.add_argument('-t', '--logtype', default='http',
                        help='log type http or https default http')
    args = parser.parse_args()
    return vars(args)


def connect_server(server, port):
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    sock.connect((server, port))
    return sock


def getLogger():
    logger = logging.getLogger()
    handler = logging.StreamHandler()
    formatter = logging.Formatter('%(asctime)s %(levelname)-4s %(message)s')
    handler.setFormatter(formatter)
    logger.addHandler(handler)
    logger.setLevel(logging.DEBUG)
    return logger


def handle_data(line, stats_dict, logtype):
    rlen = clen = blen = 0

    try:
        line = line.replace('\\', '\\\\')
        d = json.loads(line)
        domain = d.get('domain', None)

        if domain:
            rlen = d.get('request_length', 0)
            clen = d.get('content_length', 0)
            blen = d.get('body_bytes_sent', 0)

            if rlen == '-':
                rlen = 0

            if clen == '-':
                clen = 0

            if blen == '-':
                blen = 0

            try:
                rlen = int(rlen)
                clen = int(clen)
                blen = int(blen)

                if not stats_dict.get(domain, None):
                    rtime = datetime.strptime(
                        d.get('time'), '%d/%b/%Y:%H:%M:%S +0800').strftime('%Y-%m-%d')
                    stats_dict[domain] = {
                        'v': 0, 'r': 0, 'c': 0, 'b': 0, 't': rtime, 'l': logtype}

                stats_dict[domain]['v'] += 1
                stats_dict[domain]['r'] += rlen
                stats_dict[domain]['c'] += clen
                stats_dict[domain]['b'] += blen
                return stats_dict
            except ValueError as e:
                print e
    except Exception as e:
        print e

if __name__ == '__main__':
    args = getArgs()
    logger = getLogger()
    last = time.time()
    stats_dict = {}

    while True:
        line = raw_input().strip()
        stats_dict = handle_data(line, stats_dict, args.get('logtype'))

        now = time.time()
        if now - last < 60:
            continue

        if stats_dict:
            msg = json.dumps(stats_dict)
            totallen = len(msg)
            try:
                totalsent = 0
                sock = connect_server(args.get('server'), args.get('port'))
                while totalsent < totallen:
                    sent = sock.send(msg[totalsent:])
                    if sent == 0:
                        raise RuntimeError("socket connection broken")
                    totalsent = totalsent + sent
                logger.info('total send data: %d' % totalsent)
            except Exception as e:
                print e
            finally:
                sock.close()

        stats_dict = {}
        last = now
