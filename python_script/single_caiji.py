#!/usr/bin/python
# -*- coding: UTF-8 -*-
from urllib.parse import urlencode
import json
import sys
import os
import random
import time
from bs4 import BeautifulSoup
import requests
import pymysql
from qiniu import Auth, put_file, etag, urlsafe_base64_encode
import qiniu.config
import urllib.request
import re
import threading
from time import ctime,sleep
from MysqlHelper import *


supplier_id=100024;
t = time.time()
CHAPTER_TABLE_NUM=20

"""
书籍入库网址，程序会将以下参数通过 POST 方式提交

book_id 书籍ID
title 章节标题
content 章节内容
"""
MANAGE_URL = 'http://localhost/show-request-headers.php'

CUSTOM_HEADERS = {
    'user-agent': 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_3 like Mac OS X) AppleWebKit/603.3.8 (KHTML, like Gecko) Mobile/14G60 MicroMessenger/6.6.0 NetType/WIFI Language/zh_CN',
    'referer': 'https://c4240.818tu.com/auth/callback?return=read%2Fintro%2F2372&code=011YSba81XNG2U1mjV681S5T981YSba2&state=f41020c5-aac2-4a41-bb8a-fae0d22e50a4',
    'cookie': 'aliyungf_tc=AQAAACp7gUf/SQMAchrwOvHjrMZFCQy9; __fvmp=1; __trade_type=jsapi; member_token=a65e6ea23c9e44cd974da7788c676a86; Hm_lvt_d5e4918c33053db244a223b60906a4d3=1527657117; Hm_lpvt_d5e4918c33053db244a223b60906a4d3=1527657943'
}

api1="https://c4240.818tu.com/api/novels/catalog/"

#数据库配置
# database= {'host': '120.26.204.210', 'user': 'shucheng', 'password': 'shucheng','database':'shucheng'}
database= {'host': '120.26.3.61', 'user': 'shucheng', 'password': '2a4169d3d','database':'shucheng'}

mysqlHelper=MysqlHelper(database['host'],3306,database['database'],database['user'],database['password'])
# 打开数据库连接
db_global = pymysql.connect(database['host'],database['user'], database['password'], database['database'], use_unicode=True, charset="utf8")
cursor_global = db_global.cursor()
def get_url(url):
    r = requests.get(url, headers=CUSTOM_HEADERS,timeout=60)
    if r.status_code == 200:
        return r.content
def get_category(category):
    db = pymysql.connect(database['host'],database['user'], database['password'], database['database'], use_unicode=True, charset="utf8")
    sql = "select * from book_category_map where supplier_id= '%s' and supplier_category='%s'" % (supplier_id,category)
    print(sql)
    cursor = db.cursor()
    cursor.execute(sql)
    rs = cursor.fetchone()
    if rs:
        return rs
    else:
        print('没有分类信息')
        sys.exit()
    cursor.close()
    db.close()
i=2187
def add_book_chapter(supplier_book_id,insert_id):
    global i
    get_url1=api1+'%s?start=%s&limit=50&fetch_novel=1' % (supplier_book_id,i)
    print(get_url1)
    try:
        res=get_url(get_url1)
    except:
        return
    print(res)
    res=json.loads(res)
    book_info=res['data']['novel']
    print(res)

    if  res['data']['catalog']:
        print('开始采集文章列表')
        i = i + 50
        for item in res['data']['catalog']:

            #模拟用户暂时(40s-120s)

            second=random.randint(3, 8);
            print('暂停%s秒' % (second))
            sleep(second)

            print(item)
            table='book_chapter_%s' % (insert_id%CHAPTER_TABLE_NUM)
            print(table)

            book_content_url = 'https://c4240.818tu.com/read/index/%s' % (item['id'])
            print(book_content_url)
            try:
                content = get_url(book_content_url)
            except:
                break;
            print(content)
            body = []
            try:
                soup = BeautifulSoup(content, 'html.parser')
                temp1 = soup.select('.container section')
                for child in  temp1[0].children:
                    body.append(str(child))
                body = ''.join(body)
            except:
                print('文章内容异常，放弃')
                break;

            words_num = len(body)
            if(item['idx']<=20):
                price=0
                ischarge=0
            else:
                price = round(words_num / 1000 * 0.05 * 100)
                ischarge=1

            print('price....%s'%(price))
            print('ischarge....%s' % (ischarge))
            print(body)


            #查询该章节是否已经存在
            sql = "select * from %s where book_id= '%s' and chapter_order='%s'" % (table,insert_id, item['idx'])
            print(sql)
            rs = mysqlHelper.get_one(sql)
            sql = "select * from %s where book_id= '%s' and chapter_id='%s'" % ('book_content_mp',insert_id, item['idx'])
            print(sql)
            rs1 = mysqlHelper.get_one(sql)
            print(rs)
            print(rs1)
            if rs and rs1:
                print('书id%s该章节%s已经存在跳过' % (insert_id,item['id']))
                continue
            #插入书库库
            book_id=insert_id
            # 插入数据库
            # 使用cursor()方法获取操作游标
            print('插入文章章节表')
            if rs == None:
                item['title']=pymysql.escape_string(item['title'])
                sql = "INSERT INTO %s(book_id,chapter_id,chapter_order,title,price,update_time,ischarge) VALUES ('%s', '%s', '%s','%s', '%s', '%s','%s')" % (table,book_id, item['idx'], item['idx'], item['title'], price,int(t),ischarge)
                print(sql)
                mysqlHelper.insert(sql)
            if rs1 == None:
                body = pymysql.escape_string(body)
                sql = "INSERT INTO book_content_mp(book_id,chapter_id,title,content,createtime) VALUES ('%s', '%s', '%s','%s', '%s')" % (book_id, item['idx'],item['title'],body,int(t))
                print('插入文章内容表')
                print(sql)
                mysqlHelper.insert(sql)



        #模拟用户停3秒
        add_book_chapter(supplier_book_id, insert_id)

    else:
        print('没有数据')
        print('更新总章节数')
        # 更新作者
        # SQL 更新语句
        sql = "UPDATE book_pool SET chapter_count = '%s' WHERE id = '%s'" % (book_info['article_count'], insert_id)
        mysqlHelper.update(sql)
        return
    return


def upload_qiniu(url,supplier_book_id):
    path='./images/%s.jpg' % supplier_book_id
    print(path)
    f = open(path, 'wb')  # 注意第二个参数要写成wb，写成w会报错
    req = urllib.request.urlopen(url)
    buf = req.read()
    # bufstr = buf.decode('utf-8','ignore')
    f.write(buf)
    # 需要填写你的 Access Key 和 Secret Key
    access_key = 'lDXeAAdaPJ2eSqI6c6qm_QR2LMMzx8lMtOdEg47z'
    secret_key = 'CPlluEcx-sVBTfzoOekWlU2fI158lypf1yow6QZ8'
    # 构建鉴权对象
    q = Auth(access_key, secret_key)
    # 要上传的空间
    bucket_name = 'juhe-img'
    # 上传到七牛后保存的文件名
    key = 'book_m1_%s' % supplier_book_id
    # 生成上传 Token，可以指定过期时间等
    token = q.upload_token(bucket_name, key, 3600)
    # 要上传文件的本地路径
    localfile = path
    ret, info = put_file(token, key, localfile)
    assert ret['key'] == key
    assert ret['hash'] == etag(localfile)
    qiniu_path= "http://odukcaoes.bkt.clouddn.com/%s" % (key)
    req.close()  # 注意关闭response
    return qiniu_path


def main():
    supplier_book_id=907
    book_id=768
    add_book_chapter(supplier_book_id,book_id)

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print('Bye!')
