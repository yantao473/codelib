#!/usr/bin/env python
# -*- coding: utf-8 -*-

import datetime
import fcntl
import hashlib
import logging
import os
# send mail:
import smtplib
import socket
import struct
import time
import urllib
import urllib2
from email.mime.text import MIMEText
from optparse import OptionParser

import MySQLdb
import requests

from jinja2 import Template

filename = ''

# 判断读权限
os.access(filename, os.R_OK)

# 判断写权限
os.access(filename, os.W_OK)

# 判断执行权限
os.access(filename, os.X_OK)

# 判断读、写、执行权限
os.access(filename, os.R_OK | os.W_OK | os.X_OK)


# subprocess run command:
def runCmd(cmd, retry=1):
    for i in range(retry):
        try:
            p = subprocess.Popen(cmd, shell=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
            # TODO 需要再斟酌与p.stdout.readlines()的使用
            res, _ = p.communicate()
            p.wait()
            rcode = p.returncode
            if rcode == 0:
                break
        except Exception, e:
            print useStyle('run cmd %s error: %s' % (cmd, e.args[0]), fore='red')

    return rcode, res.rstrip('\n')


# urllib reqeust:
def sendReq(url, data=None):
    if data:
        data = urllib.urlencode(data)
    req = urllib2.Request(url, data=data)
    res = urllib2.urlopen(req, timeout=3)
    return res.read()


# optparse:
def getArgs():
    parser = OptionParser()
    parser.add_option("--puppet", dest="puppet", help="puppet server", default="http://puppet")
    parser.add_option("--path", dest="path", help="puppet path", default="/etc/puppet")
    parser.add_option("--deny", dest="deny", help="deny file", default="/etc/puppet/deny")
    parser.add_option("--exrun", dest="exrun", help="exrun file", default="/etc/puppet/exrun")
    (options, args) = parser.parse_args()
    return options


# mysql:
def runSql(sql):
    dbconfig = {
        'host': 'xxxx',
        'port': 3307,
        'user': 'xxxxx',
        'passwd': 'xxxx',
        'db': 'devops',
        'connect_timeout': 3,
        'charset': 'utf8',
    }

    try:
        conn = MySQLdb.connect(**dbconfig)
        cur = conn.cursor(MySQLdb.cursors.DictCursor)
        cur.execute(sql)
        return cur.fetchall()
    except MySQLdb.Error, e:
        print e
        return None
    finally:
        cur.close()
        conn.close()


# md5sum:
def fmd5(filename):
    with open(file_name, 'rb') as f:
        hashlib.md5(result).hexdigest()


# logger:
logger = logging.getLogger()
handler = logging.StreamHandler()
formatter = logging.Formatter('%(asctime)s %(levelname)-4s %(message)s')
handler.setFormatter(formatter)
logger.addHandler(handler)
logger.setLevel(logging.DEBUG)

# request uplaod:
r = requests.post(rurl, files={'uprpm': open(sname, 'rb')}, data={'ostype': ostype, 'arch': arch}, timeout=30 * 60)
print r.json()


def send_mail(content, tolist=[]):
    smtp_host = 'xxx'
    smtp_user = 'xx'
    mail_content = Template(u'''您好： {{ content }} ''').render(content=content)
    msg = MIMEText(mail_content.encode('utf8'), 'plain', _charset='utf-8')
    msg['subject'] = 'xxxx'
    msg['from'] = smtp_user
    msg['to'] = ",".join(tolist)
    s = smtplib.SMTP(smtp_host)
    s.sendmail(smtp_user, tolist, msg.as_string())
    s.quit()


# 获取本机ifname　ip ifname：　eth0
def get_ip_address(ifname):
    sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    sp = struct.pack('256s', ifname[:15])
    fd = sock.fileno()
    return socket.inet_ntoa(fcntl.ioctl(fd, 0x8915, sp)[20:24])


# 一行web server
# Python 2
# python - m SimpleHTTPServer

# Python 3
# python - m http.server

# string to timestamp
s = "01/12/2011"
time.mktime(datetime.datetime.strptime(s, "%d/%m/%Y").timetuple())
