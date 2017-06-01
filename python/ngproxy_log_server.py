#!/usr/bin/env python
# -*- coding: utf-8 -*-

import argparse
import fcntl
import json
import logging
import Queue
import socket
import struct
import threading

from redis import Redis


class Producer(threading.Thread):
    """ 默认绑定本机eth1的ip,监听8888端口 """

    def __init__(self, q, port=8888):
        super(Producer, self).__init__()
        self.q = q
        self.port = port
        self.logger = self.getLogger()

    def getLogger(self):
        logger = logging.getLogger()
        handler = logging.StreamHandler()
        formatter = logging.Formatter(
            '%(asctime)s %(levelname)-4s %(message)s')
        handler.setFormatter(formatter)
        logger.addHandler(handler)
        logger.setLevel(logging.DEBUG)
        return logger

    def get_ip_address(self, ifname):
        sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        return socket.inet_ntoa(fcntl.ioctl(sock.fileno(), 0x8915, struct.pack('256s', ifname[:15]))[20:24])

    def run(self):
        ip = self.get_ip_address('eth1')
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.bind((ip, self.port))
        sock.listen(5)

        print 'Server start at: %s:%s' % (ip, self.port)
        print 'wait for connection...'

        while True:
            con, addr = sock.accept()
            client_info = 'connect from %s:%s' % addr

            data = ''
            while True:
                chunk = con.recv(4096)
                if not len(chunk):
                    break
                data += chunk
            con.close()

            if data:
                self.logger.info(
                    client_info + ' recv data length %d' % len(data))
                self.q.put(data)


class Worker(threading.Thread):

    def __init__(self, q, rhost="localhost", rport=6379):
        super(Worker, self).__init__()
        self.q = q
        self.redis = Redis(host=rhost, port=rport)

    def run(self):
        while True:
            if not self.q.empty():
                value = self.q.get()
                try:
                    tmpdict = json.loads(value)
                    for domain in tmpdict:
                        ddict = tmpdict[domain]
                        vcnt = ddict.get('v', 0)
                        rlen = ddict.get('r', 0)
                        clen = ddict.get('c', 0)
                        blen = ddict.get('b', 0)
                        rtime = ddict.get('t')
                        logtype = ddict.get('l')

                        rkey = '%s_%s_%s' % (domain, logtype, rtime)
                        self.redis.hincrby(rkey, 'v', vcnt)
                        self.redis.hincrby(rkey, 'r', rlen)
                        self.redis.hincrby(rkey, 'c', clen)
                        self.redis.hincrby(rkey, 'b', blen)
                        # print self.redis.hgetall(domain)
                except Exception as e:
                    print e
                finally:
                    self.q.task_done()


def getArgs():
    parser = argparse.ArgumentParser(
        description='listen local port and connect redis')
    parser.add_argument('-p', '--port', type=int,
                        default=8888, help='listen local port')

    parser.add_argument('--rhost', default='localhost', help='redis server')
    parser.add_argument('--rport', type=int, default=6379, help='redis port')
    args = parser.parse_args()
    return vars(args)


def main():
    args = getArgs()

    q = Queue.Queue(0)

    p = Producer(q, args.get('port'))
    p.start()

    for i in xrange(5):
        worker = Worker(q, args.get('rhost'), args.get('rport'))
        worker.start()

if __name__ == '__main__':
    main()
