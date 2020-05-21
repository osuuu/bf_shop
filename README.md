# BF_SHOP v1.0.1

![osuu](https://oss.osuu.net/uploads/2020/04/QQ截图20200508104942.png?x-oss-process=image/quality,q_50/resize,m_fill,w_500,h_262)  

# 功能介绍
1.商品分类/上架下架/排序<br>
2.商品促销，一键设置商品促销活动<br>
3.商品上架下架/商品图片/介绍/排序<br>
4.批量添加卡密（一行一个）/删除卡密<br>
5.一键切换模板[目前1套]<br>
6.对接易支付/码支付/官方支付，可分开对接<br>
7.批量购买<br>
8.网站信息一键修改<br>
9.邮箱自动发送卡密<br>
10.商品独立分享链接<br>
11.商城出售状况邮件通知<br>
12.库存不足邮件提醒<br>
13.PDO数据库连接组件有效防止SQL注入<br>
.......

# 安装说明
上传代码至网站根目录，访问网站首页按提示安装<br>
后台登录地址： 域名/bf-login <br>
安装前需配置伪静态，否则会报错<br>
### Apache伪静态规则示例 （Nginx请自行转换）
```
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  RewriteRule ^index\.html$ - [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . /index.html [L]
</IfModule>
```

# 其他说明
程序内邮件提醒与库存监控为同步回调，如需写入异步回调请自行修改支付结果回调接口文件
### 程序作者：捕风阁 www.osuu.net
### 程序仅供交流研究，切勿用于违法用途

