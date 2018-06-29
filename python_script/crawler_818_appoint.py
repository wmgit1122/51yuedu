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

#database= {'host': '127.0.0.1', 'user': 'root', 'password': '','database':'shucheng'}
# 打开数据库连接
mysqlHelper=MysqlHelper(database['host'],3306,database['database'],database['user'],database['password'])

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
i=0
def add_book_chapter(supplier_book_id,insert_id):
    global i
    get_url1=api1+'%s?start=%s&limit=50&fetch_novel=1' % (supplier_book_id,i)
    print(get_url1)
    try:
        res=get_url(get_url1)
    except:
        return
    res=json.loads(res)
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
            #查询该章节是否已经存在
            sql = "select * from %s where book_id= '%s' and chapter_order='%s'" % (table,insert_id, item['idx'])
            rs=mysqlHelper.get_one(sql)

            sql = "select * from %s where book_id= '%s' and chapter_id='%s'" % ('book_content_m1',insert_id, item['id'])
            rs1 = mysqlHelper.get_one(sql)

            if rs and rs1:
                print('书id%s该章节%s已经存在跳过' % (insert_id,item['id']))
                continue
            #插入书库库
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
            if (item['idx'] <= 20):
                price = 0
                ischarge = 0
            else:
                price = round(words_num / 1000 * 0.05 * 100)
                ischarge = 1

            print('price....%s' % (price))
            print('ischarge....%s' % (ischarge))
            book_id=insert_id
            if rs and rs1:
                print('书id%s该章节%s已经存在跳过' % (insert_id, item['id']))
                continue


            print('插入文章章节表')
            if rs==None:
                item['title'] = str(item['title']).replace('%', '')
                item['title']=pymysql.escape_string(item['title'])
                sql = "INSERT INTO %s(book_id,chapter_id,chapter_order,title,price,update_time,ischarge) VALUES ('%s', '%s', '%s','%s', '%s', '%s','%s')" % (table,book_id, item['id'], item['idx'], item['title'], price,int(t),ischarge)
                mysqlHelper.insert(sql)
                print('插入章节成功')

            if rs1 == None:
                body = body.replace('%', '')
                body = pymysql.escape_string(body)
                sql = "INSERT INTO book_content_m1(supplier_id,book_id,chapter_id,content,createtime) VALUES ('%s', '%s', '%s','%s', '%s')" % (supplier_id,book_id, item['id'],body,int(t))
                mysqlHelper.insert(sql)
                print('插入章节内容成功')

        #模拟用户停3秒
        print('接口暂停3秒')
        sleep(3)
        add_book_chapter(supplier_book_id, insert_id)

    else:
        print('没有数据')
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

def get_book_info(supplier_book_id):
    get_book_info_url = api1 + '%s?start=%s&limit=1&fetch_novel=1' % (supplier_book_id, 0)
    res = get_url(get_book_info_url)
    res=json.loads(res)
    book_info=res['data']['novel']
    return  book_info

def main(book_list):
    if book_list:
        for item in book_list:
            supplier_book_id=item['id']
            book_name=item['title']
            #如果图书已经存在则跳过
            sql = "select * from book_pool where supplier_id='%s' and supplier_book_id= '%s'" % (supplier_id,supplier_book_id)
            rs = mysqlHelper.get_one(sql)
            print(rs)
            global i
            # 获取图书信息
            book_info = get_book_info(supplier_book_id)
            if rs:
                print('%s书已经存在' % (book_name))
                #更新图书最新章节数
                print('更新总章节数')
                # SQL 更新语句
                sql = "UPDATE book_pool SET chapter_count = '%s' WHERE supplier_book_id = '%s' and supplier_id='%s' "  % (book_info['article_count'], supplier_book_id,supplier_id)
                print(sql)
                mysqlHelper.update(sql)
                print('扫描不完成的章节,跳过入库图书表')
                #如果章节和内容都存在，则跳过
                print(book_info['article_count'])
                table='book_chapter_%s' % (rs[0]%CHAPTER_TABLE_NUM)
                print(table)

                #查询该章节是否已经存在
                sql = "select count(*) as count,book_id from %s where book_id= '%s' " % (table,rs[0])
                rs2 =mysqlHelper.get_one(sql)
                print(rs2[0])
                sql = "select count(*) as count  from %s where book_id= '%s' " % ('book_content_m1',rs[0])
                rs1 = mysqlHelper.get_one(sql)
                print(rs1[0])
                if rs2[0]==book_info['article_count'] and rs1[0]==book_info['article_count']:
                    print('书id%s所有章节都已经存在跳过' % (rs[0]))
                    continue
                else:
                    #计算最后一章是多少
                    print('计算最后一章是多少')
                    sql = "select chapter_order from %s where book_id= '%s' order by chapter_order desc limit 1 " % (table, rs[0])
                    print(sql)
                    book_one = mysqlHelper.get_one(sql)
                    print(book_one)
                    if book_one:
                        i = int(book_one[0])
                    else:
                        i = 0
                    print('从第%s章开始抓取' % (i))
                    add_book_chapter(supplier_book_id, rs[0])
                    time.sleep(1)
            else:
                # 图片上传七牛
                cover_img = upload_qiniu(book_info['avatar'], supplier_book_id)

                author = book_info['author'];  # 暂时没有作者
                intro = book_info['summary']
                # cover_img=item['avatar']
                progress = 0 if book_info['status'] == 'ongoing' else 1  # 1完结 0连载
                # 获取所属分类
                cate_info = get_category(item['category'])
                category_id = cate_info[3]  # 所属分类
                channel_id = 2 if book_info['gender'] == 'female' else 1
                chapter_count = book_info['article_count']
                numofchars = book_info['words']
                updatetime = int(t)
                charge_mode = 2

                # 插入数据库
                # 使用cursor()方法获取操作游标
                db = pymysql.connect(database['host'], database['user'], database['password'], database['database'],
                                     use_unicode=True, charset="utf8")
                cursor = db.cursor()
                sql = "INSERT INTO book_pool(supplier_id, supplier_book_id, book_name,author,intro,cover_img,progress,category_id,channel_id,chapter_count,numofchars,updatetime,charge_mode)" \
                      " VALUES ('%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s','%s', '%s', '%s','%s')" % \
                      (supplier_id, supplier_book_id, book_name, author, intro, cover_img, progress, category_id,
                       channel_id, chapter_count, numofchars, updatetime, charge_mode)

                # 使用execute方法执行SQL语句
                try:
                    cursor.execute(sql)
                    # 提交
                    db.commit()
                except:
                    continue
                # 添加新书
                insert_id = int(cursor.lastrowid)
                cursor.close()
                # 从0开始
                i = 0
                add_book_chapter(supplier_book_id, insert_id)

                # 关闭数据库连接
                db.close()
    else:
        print('没有数据了')
        return

if __name__ == '__main__':
    try:
        # gender=>female女士  male男士
        # book_list = [
        #     {'id':2422,'title':'心底的山盟海誓','category':'都市'},
        #     {'id':2662,'title':'爱你心如刀绞','category':'都市'},
        #     {'id':2582,'title':'此间红尘喧嚣','category':'都市'},
        #     {'id': 2497, 'title': '傲娇女老师', 'category': '都市'},
        #     {'id': 2490, 'title': '女上司的全能助理', 'category': '都市'},
        #     {'id': 2477, 'title': '狱情', 'category': '都市'}
        # ]
        # book_list = [
        #     {'id':1571,'title':'吻安顾先生','category':'都市'},
        #     {'id':2858,'title':'念你此生无憾','category':'都市'},
        #     {'id':2866,'title':'山盟海誓的曾经','category':'都市'},
        #     {'id': 2845, 'title': '共度余生只有你', 'category': '都市'},
        #     {'id': 1642, 'title': '时光几许烟雨', 'category': '都市'},
        #     {'id': 2763, 'title': '情缘随风散去', 'category': '都市'},
        #     {'id': 2810, 'title': '妙手贤妃', 'category': '都市'},
        #     {'id': 2565, 'title': '至强神瞳', 'category': '都市'}
        # ]

        book_list = [
            # {'id':3498,'title':'摇曳花瓣爱落泪','category':'都市'}
            #{'id': 3561, 'title': '眼前你是梦中人', 'category': '都市'}
            # {'id': 3554, 'title': '没有想到的秘密', 'category': '都市'}
            # {'id': 3512, 'title': '爱如山崩地裂', 'category': '都市'}
            # {'id': 3535, 'title': '千言万语杯莫停', 'category': '都市'}
            # {'id': 2477, 'title': '狱情', 'category': '都市'}
            # {'id': 3535, 'title': '风清起缘善飘落', 'category': '都市'}
            # {'id': 3654, 'title': '烟雨缥缈江南情', 'category': '都市'}
            # {'id': 3557, 'title': '绝代狂徒', 'category': '都市'}
            # {'id': 3221, 'title': '王者归都', 'category': '都市'}
            {'id': 2477, 'title': '狱情', 'category': '都市'}

        ]
        main(book_list)
    except KeyboardInterrupt:
        print('Bye!')