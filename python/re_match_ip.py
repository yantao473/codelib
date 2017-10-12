#!/usr/bin/env python
# -*- coding: utf-8 -*-

import re

if __name__ == '__main__':
    ipok = '10.253.144.221'
    ipbad = '10.313.144.251'

    basesec = '(25[0-5]|2[0-4]\d|[01]?\d{1,2})'
    restr = r'%s\.%s\.%s\.%s' % (basesec, basesec, basesec, basesec)
    pattern = re.compile(restr)
    r1 = re.match(pattern, ipok)
    r2 = re.match(pattern, ipbad)

    if r1:
        print r1.group()
    if r2:
        print r2.group()

    r1 = re.search(pattern, ipok)
    r2 = re.search(pattern, ipbad)

    if r1:
        print r1.group()
    if r2:
        print r2.group()
