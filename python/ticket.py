#!/usr/bin/env python
# -*- coding: utf-8 -*-


import argparse
import json
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


def UseStyle(string, mode='', fore='', back=''):

    mode = '%s' % STYLE['mode'][mode] if mode in STYLE['mode'] else ''
    fore = '%s' % STYLE['fore'][fore] if fore in STYLE['fore'] else ''
    back = '%s' % STYLE['back'][back] if back in STYLE['back'] else ''
    style = ';'.join([s for s in [mode, fore, back] if s])
    style = '\033[%sm' % style if style else ''
    end = '\033[%sm' % STYLE['default']['end'] if style else ''
    return '%s%s%s' % (style, string, end)


def getargs():
    parser = argparse.ArgumentParser(description='输入订票日期和车次进行查询')
    parser.add_argument('-d', '--date', type=str, help='date', required=True)
    parser.add_argument('-t', '--trips',  type=str, help="trips", required=True)
    args = parser.parse_args()
    return args


def get_data(d, t):
    base_url = "https://kyfw.12306.cn/otn/leftTicket/query?leftTicketDTO.train_date=%s" % d
    params = "&leftTicketDTO.from_station=VNP&leftTicketDTO.to_station=WWP&purpose_codes=ADULT"
    url = '%s%s' % (base_url, params)
    r = requests.get(url)
    try:
        tmpdict = json.loads(r.text)
        data = tmpdict.get('data')
        result = data.get('result')

        m = ''
        for i in range(len(result)):
            if t in result[i]:
                m = result[i]
                break
        if m:
            tmplist = m.split('|')
            first = tmplist[31]
            second = tmplist[30]

            h = u'有'
            n = u'无'
            if first == n or second == n:
                print UseStyle('%s %s 无票' % (d, t), mode='bold', fore='red')
            elif second == h or (second.isalnum() and int(second) > 0):
                print UseStyle('%s %s 有二等座票' % (d, t), mode='bold', fore='green')
            elif first == h or (first.isalnum() and int(first) > 0):
                print UseStyle('%s %s 有一等座票' % (d, t), mode='bold', fore='green')
    except Exception, e:
        print e


def main():
    args = getargs()
    while True:
        get_data(args.date, args.trips)


if __name__ == '__main__':
    main()
