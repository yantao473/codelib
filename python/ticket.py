#!/usr/bin/env python
# -*- coding: utf-8 -*-

import argparse
import datetime
import json
import os
import re
import time

import requests

import prettytable as pt


def get_stations_code():
    domain = 'https://kyfw.12306.cn'
    action = '/otn/index/init'
    headers = {
        'host': 'kyfw.12306.cn',
        'user-agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84',
        'referer': 'https://kyfw.12306.cn/otn/leftTicket/init'
    }
    url = '%s%s' % (domain, action)
    r = requests.get(url, headers=headers, timeout=3)

    m = re.search(r'station_version=(\d+?\.\d+)', r.text)
    if m:
        mapdict = {}
        saction = '/otn/resources/js/framework/station_name.js?%s' % m.group()
        surl = '%s%s' % (domain, saction)
        sr = requests.get(surl, headers=headers, timeout=3)
        c = sr.text.replace('var', '').replace('station_names', '').replace(
            '=', '').replace("'", '').replace(';', '').strip().lstrip('@')
        slist = c.split('@')
        for s in slist:
            t = s.split('|')
            mapdict[t[1]] = t[2]

        if mapdict:
            with open('station_code.txt', 'w') as f:
                f.write(json.dumps(mapdict))


def getargs():
    curtime = datetime.datetime.now().strftime('%Y-%m-%d')
    parser = argparse.ArgumentParser(description='输入订票日期和车次进行查询')
    parser.add_argument('-d', '--train_date', type=str,  default=curtime, help='出发时间')
    parser.add_argument('-t', '--train_number', type=str, default='C2239', help='车次')
    parser.add_argument('-s', '--start_station', type=str, default='北京南', help='出发地')
    parser.add_argument('-e', '--end_station', type=str, default='武清', help='目的地')
    parser.add_argument('-u', '--update_stations', type=bool, default=False, help='更新站点编码列表')
    args = parser.parse_args()
    return args


def get_data(sdate, trainNumber, sstation, estation):
    domain = 'https://kyfw.12306.cn/otn/leftTicket/query'
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

            # green = '\033[32;49;1m%s\033[0m'
            yellow = '\033[33;49;1m%s\033[0m'
            red = '\033[31;49;1m%s\033[0m'

            if t[26] == u'无':
                t[26] = red % t[26]
            else:
                t[26] = yellow % t[26]

            if t[30] == u'无':
                t[30] = red % t[30]
            else:
                t[30] = yellow % t[30]

            if t[31] == u'无':
                t[31] = red % t[31]
            else:
                t[31] = yellow % t[31]

            tb.add_column(u'车次', [t[3]])
            tb.add_column(u'日期', [sdate[5:]])
            tb.add_column(u'发车时间', [t[8]])
            # tb.add_column(u'到站时间', [t[9]])
            tb.add_column(u'无座', [t[26]])
            tb.add_column(u'二等座', [t[30]])
            tb.add_column(u'一等座', [t[31]])
            tb.add_column(u'商务座', [shangwu])
            tb.add_column(u'标识', [str(int(time.time()))[-2:]])

            # for i in range(len(t)):
            #     print '%d --- %s' % (i, t[i])
            # print('-'*200)
            print(tb)

    except Exception as e:
        # print(e)
        time.sleep(10)


def main():
    args = getargs()
    sfile_name = 'station_code.txt'
    if not os.path.exists(sfile_name) or not os.path.getsize(sfile_name) or args.update_stations:
        get_stations_code()

    station_mapdict = {}
    with open(sfile_name) as f:
        station_mapdict = json.loads(f.read())

    sstation = args.start_station.decode('utf-8')
    estation = args.end_station.decode('utf-8')

    sdate = args.train_date
    trainNumber = args.train_number

    sstation_code = station_mapdict.get(sstation)
    estation_code = station_mapdict.get(estation)

    while True:
        get_data(sdate, trainNumber, sstation_code, estation_code)
        time.sleep(3)


if __name__ == '__main__':
    main()
