#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys


# 对于一条命令LPUSH llist_mail 1075451854 转为 redis protocol的方法
# 1. 先将命令转换为 ['LPUSH','llist_mail','1075451854']
# 2. 计算list长度为x 记下*x\r\n
# 3. 计算list每一项a的长度记为k 记下$k\r\na\r\n
# 4. 将两次记下的字串连接 *x\r\n$k\r\na\r\n
# 将生成的文件用cat xxx_protocol.txt | redis-cli --pipe  批量插入到redis中


def main():
    if len(sys.argv) != 2:
        print 'Usage %s mail|weibo|weipan' % sys.argv[0]
        exit(0)

    prefix = sys.argv[1]
    fn = '%s.txt' % prefix
    cmdfn = '%s_redis_protocol.txt' % prefix
    listName = 'llist_%s' % prefix
    llen = len(listName)

    data = []
    with open(fn) as f:
        for line in f:
            line = line.rstrip('\n')
            data.append('*3\r\n$5\r\nlpush\r\n$%d\r\n%s\r\n$%d\r\n%s\r\n' % (llen, listName, len(line), line))

    with open(cmdfn, 'w') as f:
        f.write(''.join(data))
        pass


if __name__ == '__main__':
    main()
