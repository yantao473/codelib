#!/usr/bin/env python
# -*- coding: utf-8 -*-
import argparse
import json
import re
import time

import requests

import prettytable as pt


def getargs():
    parser = argparse.ArgumentParser(description='输入订票日期和车次进行查询')
    parser.add_argument('-d', '--train_date', type=str, help='出发时间', required=True)
    parser.add_argument('-t', '--train_number', type=str, default='C2239', help='车次')
    parser.add_argument('-s', '--start_station', type=str, default='VNP', help='出发地 北京南为VNP')
    parser.add_argument('-e', '--end_station', type=str, default='WWP', help='目的地 武清为WWP')
    args = parser.parse_args()
    return args


def get_data(args):
    sdate = args.train_date
    trainNumber = args.train_number
    sstation = args.start_station
    estation = args.end_station

    domain = 'https://kyfw.12306.cn/otn/leftTicket/queryO'
    tdate = 'leftTicketDTO.train_date=%s' % sdate
    cfrom = 'leftTicketDTO.from_station=%s' % sstation
    cto = 'leftTicketDTO.to_station=%s' % estation
    url = '%s?%s&%s&%s&purpose_codes=ADULT' % (domain, tdate, cfrom, cto)

    headers = {
        'host': 'kyfw.12306.cn',
        'user-agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84',
        'referer': 'https://kyfw.12306.cn/otn/leftTicket/init'
    }

    r = requests.get(url, headers=headers, timeout=3)
    try:
        tmpdict = json.loads(r.text)
        data = tmpdict.get('data')
        result = data.get('result')

        m = ''
        for i in range(len(result)):
            if trainNumber in result[i]:
                m = result[i]
                break
        if m:
            tb = pt.PrettyTable()
            t = m.split('|')

            shangwu = t[25]
            if not shangwu:
                shangwu = t[32]

            tb.add_column(u'    车次', [t[3]])
            tb.add_column(u'乘车日期', [sdate])
            tb.add_column(u'发车时间', [t[8]])
            tb.add_column(u'到站时间', [t[9]])
            tb.add_column(u'    无座', [t[26]])
            tb.add_column(u'  二等座', [t[30]])
            tb.add_column(u'  一等座', [t[31]])
            tb.add_column(u'  商务座', [shangwu])

            # for i in range(len(t)):
            #     print '%d --- %s' % (i, t[i])
            # print('-'*200)
            print(tb)

    except Exception as e:
        print(e)


def main():
    print('北京南查询编码为: VNP，武清查询编码为: WWP')
    args = getargs()
    while True:
        get_data(args)
        time.sleep(3)


if __name__ == '__main__':
    main()
