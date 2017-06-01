#!/usr/bin/env python
# -*- coding: utf-8 -*-

# 适用于ftpproxy 2.1.0-beta4


import commands
import re
import sys
import time


def check_base_env():
    # check whether ipset exists
    status, _ = commands.getstatusoutput('ipset list ftpproxy')
    if status:
        # print 'ipset not exists'
        status, result = commands.getstatusoutput(
            'ipset create ftpproxy hash:ip hashsize 4096 maxelem 1000000 timeout 86400')
        if status:
            print result
            sys.exit(1)

    # check whether iptables exists
    status, _ = commands.getstatusoutput('iptables-save | grep ftpproxy')
    if status:
        # print 'iptables not exists'
        status, result = commands.getstatusoutput(
            'iptables -I INPUT -m set --match-set ftpproxy src -p tcp --match multiport --dports 21,10121 -j DROP')
        if status:
            print result
            sys.exit(1)


def add_to_ipset(ip):
    status, _ = commands.getstatusoutput('ipset add ftpproxy %s' % ip)
    if status == 0:
        print 'add success'


if __name__ == '__main__':
    # create ipset and iptables
    check_base_env()

    buff = ''
    stats_dict = {}
    pattern = re.compile(r'client=\s*?(?P<ip>\d+\.\d+\.\d+\.\d+)')
    errflag = 'ftpproxy: -ERR: reply to PASS: 530 Login incorrect'

    try:
        while True:
            buff += sys.stdin.read(1)
            if buff.endswith('\n'):
                if errflag in buff:
                    m = pattern.search(buff[:-1])
                    client_ip = m.groupdict().get('ip').strip()

                    if not stats_dict.get(client_ip, None):
                        stats_dict[client_ip] = {
                            'times': 1, 'ctime': int(time.time())}
                    else:
                        stats_dict[client_ip]['times'] += 1

                if stats_dict:
                    now = int(time.time())
                    for ip, tdict in stats_dict.items():
                        times = tdict.get('times', 0)
                        ctime = tdict.get('ctime', 0)

                        if times >= 50:
                            add_to_ipset(ip)
                            del stats_dict[ip]

                        if now - ctime >= 86400:
                            del stats_dict[ip]

                buff = ''
    except KeyboardInterrupt:
        sys.stdout.flush()
