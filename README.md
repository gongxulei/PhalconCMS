PhalconCMS
=================
### 介绍
基于Phalcon的博客CMS

### 推荐环境
* PHP 5.6+
* mysql 5.6+
* phalcon 3.0+

### 安装步骤
* 在数据库中导入phalconCMS.sql文件
* 添加nginx配置，demo:
```bash
	server {
	    listen 80;
	    server_name test.com;
	    root /path/PhalconCMS/public;
	    index index.php index.html index.htm;
	
	    location / {
	        if ($request_uri ~ (.+?\.php)(|/.+)$ ) {
	            break;
	        }
	
	        if (!-e $request_filename) {
	            rewrite ^/(.*)$ /index.php?_url=/$1;
	        }
	    }
	
	    location ~ \.php {
	        fastcgi_pass  unix:/tmp/php-cgi.sock;
	        fastcgi_index index.php;
	        include fastcgi_params;
	        set $real_script_name $fastcgi_script_name;
	        if ($fastcgi_script_name ~ "^(.+?\.php)(/.+)$") {
	            set $real_script_name $1;
	            set $path_info $2;
	        }
	        fastcgi_param SCRIPT_FILENAME $document_root$real_script_name;
	        fastcgi_param SCRIPT_NAME $real_script_name;
	        fastcgi_param PATH_INFO $path_info;
	    }
	
	    access_log  /path/logs/PhalconCMS/access.log  access;
	    error_log  /path/logs/PhalconCMS/error.log;
	}
```
* 修改app/cache目录权限：chmod -R 0777 app/cache
* 修改app/config/system/system_{$runtime}.php中的数据库配置
* 后台(http://www.xxx.com/admin/index/index)登录账号密码：admin  123456
* 在后台的“站点管理-基本设置”中修改“站点地址”、“CDN地址”等

#### 环境匹配设置
* 在不同环境（开发、测试、线上）上运行此项目时，请修改index.php中的``` "$runtime" ```值：
``` dev:开发   test:测试    pro:线上 ```程序会根据此变量，自动匹配环境所需的配置（api,system）文件

#### 作者
[www.marser.cn][2] (http://www.marser.cn)

#### QQ群
* 广州PHP高端交流群：158587573
* Phalcon玩家群：150237524


[1]:	http://www.iphalcon.cn
[2]:	http://www.marser.cn
