#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os

filename = ''

# 判断读权限
os.access(filename, os.R_OK)

# 判断写权限
os.access(filename, os.W_OK)

# 判断执行权限
os.access(filename, os.X_OK)

# 判断读、写、执行权限
os.access(filename, os.R_OK | os.W_OK | os.X_OK)
