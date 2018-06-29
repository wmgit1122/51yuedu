#!/usr/bin/python
# -*- coding: UTF-8 -*-
import pymysql
from MysqlHelper import *
import requests
import json
import time
from bs4 import BeautifulSoup
from WechatPush import *


CHAPTER_TABLE_NUM=20

MANAGE_URL = 'http://localhost/show-request-headers.php'

CUSTOM_HEADERS = {
    'user-agent': 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_3 like Mac OS X) AppleWebKit/603.3.8 (KHTML, like Gecko) Mobile/14G60 MicroMessenger/6.6.0 NetType/WIFI Language/zh_CN',
    'referer': 'https://c4240.818tu.com/auth/callback?return=read%2Fintro%2F2372&code=011YSba81XNG2U1mjV681S5T981YSba2&state=f41020c5-aac2-4a41-bb8a-fae0d22e50a4',
    'cookie': 'Hm_lvt_d5e4918c33053db244a223b60906a4d3=1522652903,1522652911,1522653036,1522735272; size=large; aliyungf_tc=AQAAAJPLPHMZdAIAchrwOpvwlHHBjipB, aliyungf_tc=AQAAAER3q0MfewEAchrwOpA8agABRfRl; __fvmp=1; __trade_type=jsapi; member_token=3a0eb9f68428433893c1c1cd0e712d71'
}
WechatPush = WechatPush('wxacda9688d803298f', 'd6660e5ccf08ef57a133587694fcbe81')

def get_url(url):
    r = requests.get(url, headers=CUSTOM_HEADERS,timeout=60)
    if r.status_code == 200:
        return r.content

def get_book_info(supplier_book_id):
    get_book_info_url = book_info_url + '%s?start=%s&limit=1&fetch_novel=1' % (supplier_book_id, 0)
    res = get_url(get_book_info_url)
    res=json.loads(res)
    book_info=res['data']['novel']
    return  book_info


def add_book_chapter(supplier_book_id,insert_id):
    global i
    get_url1=book_info_url+'%s?start=%s&limit=50&fetch_novel=1' % (supplier_book_id,i)
    print(get_url1)
    try:
        res=get_url(get_url1)
    except:
        return
    res=json.loads(res)
    book_info = res['data']['novel']
    print(res)
    if  res['data']['catalog']:
        print('开始采集文章列表')
        i = i + 50
        for item in res['data']['catalog']:
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
            book_id=insert_id

            words_num = len(body)
            if (item['idx'] <= 20):
                price = 0
                ischarge = 0
            else:
                price = round(words_num / 1000 * 0.05 * 100)
                ischarge = 1

            print('插入文章章节表')
            if rs==None:
                item['title']=pymysql.escape_string(item['title'])
                sql = "INSERT INTO %s(book_id,chapter_id,chapter_order,title,price,update_time,ischarge) VALUES ('%s', '%s', '%s','%s', '%s', '%s','%s')" % (table,book_id, item['id'], item['idx'], item['title'], price,int(t),ischarge)
                mysqlHelper.insert(sql)
                print('插入章节成功')

            if rs1 == None:
                body = pymysql.escape_string(body)
                sql = "INSERT INTO book_content_m1(supplier_id,book_id,chapter_id,content,createtime) VALUES ('%s', '%s', '%s','%s', '%s')" % (supplier_id,book_id, item['id'],body,int(t))
                mysqlHelper.insert(sql)
                print('插入章节内容成功')

        #模拟用户停3秒
        print('暂停1秒')
        time.sleep(1)
        add_book_chapter(supplier_book_id, insert_id)

    else:
        print('没有数据,微信通知用户更新到多少章节')
        #获取所以阅读过该书的用户
        # sql = 'select *  from book_read_record where book_id=%s order by id asc ' % (insert_id)
        # read_list = mysqlHelper.get_all(sql)
        # print(read_list)

        # for item in read_list:
        #     #获取用户的信息(夜书仿)
        #     sql='select *  from account where  accountid=%s and channel_id=1 and (login_type=2 or login_type=4)' % (item[1])
        #     member_info=mysqlHelper.get_one(sql)
        #     if member_info:
        #         #给用户发送微信通知
        #         #通知用户更新章节
        #         data = {
        #             "first": {"value": "尊敬的读者上午好", "color": "#173177"},
        #             "keyword1": {"value": "好书推荐", "color": "#173177"},
        #             "keyword2": {"value": "《%s》" % (book_info['title']), "color": "#173177"},
        #             "remark": {"value": "%s" % (book_info['summary']), "color": "#173177"}
        #         }
        #         redirect_url='http://www.mpyuedu.com/index.php?/book/book_info/content/%s/0/reading/%s' % (item[2],member_info[1])
        #         print(redirect_url)
        #
        #         date=time.strftime("%Y-%m-%d", time.localtime())
        #         #判断推送过则跳过
        #         sql="select * from wechat_notice_log where accountid='%s' and DATE_FORMAT(FROM_UNIXTIME(addtime),'%%Y-%%m-%%d') ='%s'" % (member_info[0],date)
        #         sql=sql.replace("%", "%%")
        #         exists_push=mysqlHelper.get_one(sql)
        #         print(exists_push)
        #         if exists_push:
        #             print('今天已经推送过了')
        #         else:
        #             errmsg = WechatPush.do_push_tmp(member_info[2], 'AuYwqijOe9DkBIY1IfDxpuwu-5l8JweNv_CAcDOEpJo',redirect_url, data, '#7B68EE')
        #             if errmsg=='ok':
        #                 #插入数据库
        #                 sql = "INSERT INTO wechat_notice_log(accountid,book_id,addtime) VALUES ('%s', '%s', '%s')" % (member_info[0], item[2],int(t))
        #                 mysqlHelper.insert(sql)
        #                 print('微信推送成功')
        #
        #     else:
        #         print('用户不是微信用户')

        progress = 0 if book_info['status'] == 'ongoing' else 1  # 1完结 0连载
        #更新图书信息
        sql = "UPDATE book_pool SET chapter_count = '%s',progress='%s' WHERE supplier_id=100024 and  id = '%s'" % (book_info['article_count'],progress, insert_id)
        print(sql)
        mysqlHelper.update(sql)
        print('图书更新成功')

        return
    return



t = time.time()
supplier_id=100024
i=0
progress=0
book_info_url="https://c4240.818tu.com/api/novels/catalog/"
#数据库配置
# database= {'host': '120.26.204.210', 'user': 'shucheng', 'password': 'shucheng','database':'shucheng'}
database= {'host': 'rm-bp128gc7rp6s4g1pvo.mysql.rds.aliyuncs.com', 'user': 'wangmin', 'password': 'wangmin123','database':'shucheng'}

params=[1,2]
sql='select *  from book_pool where  supplier_id=100024 and  progress=%s order by id asc '% (progress)
mysqlHelper=MysqlHelper(database['host'],3306,database['database'],database['user'],database['password'])
list=mysqlHelper.get_all(sql)
for item in list:
    book_info=get_book_info(item[2])
    chapter_count=item[10]
    print(chapter_count)
    print(book_info['article_count'])
    if(book_info['article_count'] != chapter_count):
        #书源章节已更新,采集最新章节
        # 计算最后一章是多少
        print(item)
        table = 'book_chapter_%s' % (item[0] % CHAPTER_TABLE_NUM)
        print(table)
        sql = "select chapter_order from %s where book_id= '%s' order by chapter_order desc limit 1 " % (table, item[0])
        print(sql)
        book_one = mysqlHelper.get_one(sql)
        print(book_one)
        if book_one:
            i = int(book_one[0])
        else:
            i = 0
        print('从第%s章开始抓取' % (i))
        print('书id%s章节不全，共%s章' % (item[0], book_info['article_count']))
        add_book_chapter(item[2], item[0])
    else:
        print('该书%s所有章节已经存在' % (item[3]))










