#/usr/bin/python
#-*- coding: utf-8 -*-
import hashlib
import random
import time
from multiprocessing import Pool


def f(x):
  while x > 0:
    for i in range(100000):
      num = str(random.randint(10000000000000000, 90000000000000000))
      md5 = hashlib.md5(num).hexdigest()
    time.sleep(random.random())


if __name__ == '__main__':
  p = Pool()
  p.map(f, [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1])
