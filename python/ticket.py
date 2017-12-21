#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
stattion_version可以在页面https://kyfw.12306.cn/otn/index/init中按F12选中网络(Network)项
将station_name.js?station_version=xxxxx 将xxxx写入即可
"""

import argparse
import json
import string
import random
import re
import time
import requests

#   格式：\033[显示方式;前景色;背景色m
#   说明:
#
#   前景色            背景色            颜色
#   ---------------------------------------
#     30                40              黑色
#     31                41              红色
#     32                42              绿色
#     33                43              黃色
#     34                44              蓝色
#     35                45              紫红色
#     36                46              青蓝色
#     37                47              白色
#
#   显示方式           意义
#   -------------------------
#      0           终端默认设置
#      1             高亮显示
#      4            使用下划线
#      5              闪烁
#      7             反白显示
#      8              不可见
#
#   例子：
#   \033[1;31;40m    <!--1-高亮显示 31-前景色红色  40-背景色黑色-->
#   \033[0m          <!--采用终端默认设置，即取消颜色设置-->]]]


STYLE = {
    'fore':
        {   # 前景色
            'black': 30,  # 黑色
            'red': 31,  # 红色
            'green': 32,  # 绿色
            'yellow': 33,  # 黄色
            'blue': 34,  # 蓝色
            'purple': 35,  # 紫红色
            'cyan': 36,  # 青蓝色
            'white': 37,  # 白色
        },

    'back':
        {   # 背景
            'black': 40,  # 黑色
            'red': 41,  # 红色
            'green': 42,  # 绿色
            'yellow': 43,  # 黄色
            'blue': 44,  # 蓝色
            'purple': 45,  # 紫红色
            'cyan': 46,  # 青蓝色
            'white': 47,  # 白色
        },

    'mode':
        {   # 显示模式
            'mormal': 0,  # 终端默认设置
            'bold': 1,  # 高亮显示
            'underline': 4,  # 使用下划线
            'blink': 5,  # 闪烁
            'invert': 7,  # 反白显示
            'hide': 8,  # 不可见
        },

    'default':
        {
            'end': 0,
        },
}


def useStyle(string, mode='', fore='', back=''):
    mode = '%s' % STYLE['mode'][mode] if mode in STYLE['mode'] else ''
    fore = '%s' % STYLE['fore'][fore] if fore in STYLE['fore'] else ''
    back = '%s' % STYLE['back'][back] if back in STYLE['back'] else ''
    style = ';'.join([s for s in [mode, fore, back] if s])
    style = '\033[%sm' % style if style else ''
    end = '\033[%sm' % STYLE['default']['end'] if style else ''
    return '%s%s%s' % (style, string, end)


def getargs():
    parser = argparse.ArgumentParser(description='输入订票日期和车次进行查询')
    parser.add_argument('-d', '--date', type=str, help='出发时间', required=True)
    parser.add_argument('-t', '--trips',  type=str, help='车次', required=True)
    parser.add_argument('-s', '--sstation',  type=str, default='北京南', help='出发地')
    parser.add_argument('-e', '--estation',  type=str, default='武清', help='目的地')
    parser.add_argument('-v', '--version',  type=str, default='1.9035', help='station_version版本')
    args = parser.parse_args()
    return args


def get_data(sdate, trips, sstation, estation):
    base_url = "https://kyfw.12306.cn/otn/leftTicket/query?leftTicketDTO.train_date=%s" % sdate
    params = "&leftTicketDTO.from_station=%s&leftTicketDTO.to_station=%s&purpose_codes=ADULT" % (sstation, estation)
    url = '%s%s' % (base_url, params)
    headers = {
        'host': 'kyfw.12306.cn',
        'user-agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36',
        'referer': 'https://kyfw.12306.cn/otn/leftTicket/init'
    }

    r = requests.get(url, headers=headers, timeout=3)
    try:
        tmpdict = json.loads(r.text)
        data = tmpdict.get('data')
        result = data.get('result')

        m = ''
        for i in range(len(result)):
            if trips in result[i]:
                m = result[i]
                break
        if m:
            tmplist = m.split('|')

            shangwu = tmplist[32]
            first = tmplist[31]
            second = tmplist[30]
            no = tmplist[26]

            # h = u'有'  # 有票
            n = u'无'  # 无票

            rlist = []
            for i in range(6):
                rlist.append(random.choice(string.letters + string.digits))
            rstr = ''.join(rlist)

            msg = '日期: %s 车次: %s 商务座: %s 一等座: %s 二等座: %s 无座: %s' % (sdate, trips, shangwu.encode(
                'utf8'), first.encode('utf8'), second.encode('utf8'), no.encode('utf8'))
            if first == n and second == n and no == n:
                print useStyle('%s %s' % (msg, rstr), mode='bold', fore='red')
            else:
                print useStyle('%s %s' % (msg, rstr), mode='bold', fore='green')
    except Exception, e:
        print e


def get_station_js():
    url = 'https://kyfw.12306.cn/otn/index/init'
    headers = {
        'host': 'kyfw.12306.cn',
        'user-agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36',
        'referer': 'https://kyfw.12306.cn/otn/leftTicket/init'
    }

    r = requests.get(url, headers=headers, timeout=3)
    pattern = re.compile(r'/otn/resources/js/framework/station_name\.js\?station_version=\d+?\.\d+', re.M | re.I)
    m = pattern.search(r.text)
    if m:
        return m.group()
    else:
        return None


def get_stations(url):
    headers = {
        'host': 'kyfw.12306.cn',
        'user-agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36',
        'referer': 'https://kyfw.12306.cn/otn/leftTicket/init'
    }

    r = requests.get(url, headers=headers, timeout=3)
    info = r.text.replace('var', '') .replace('station_names', '') .replace(
        '=', '') .replace("'", '') .replace(";", '') .strip()
    return info.split('|')


def get_station_map(station_list, station):
    if isinstance(station, str):
        station = station.decode('utf-8')
    try:
        mindex = station_list.index(station)
        return station_list[mindex + 1]
    except Exception:
        return None


def main():
    print useStyle('注意: 使用时请传入正确的出发地和目的地编码，默认为出发地为北京南，目的地为武清', mode='bold', fore='yellow')
    args = getargs()
    c = 0
    try_times = 3
    while c < try_times:
        js = get_station_js()
        if js:
            stations_url = 'https://kyfw.12306.cn%s' % js
            break
        c = c + 1
    else:
        v = raw_input('请输入station_name.js的版本:')
        stations_url = 'https://kyfw.12306.cn/otn/resources/js/framework/station_name\.js\?station_version=%s' % v

    stations = get_stations(stations_url)
    sstation = get_station_map(stations, args.sstation)
    estation = get_station_map(stations, args.estation)

    while True:
        get_data(args.date, args.trips, sstation, estation)
        time.sleep(3)


if __name__ == '__main__':
    main()
