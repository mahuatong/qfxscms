# 狂雨小说  清风小说CMS



> 运行环境要求PHP7.2+，mysql,redis

> 使用thinkphp6+swoole搭建，改自狂雨小说CMS [官方链接](http://bbs.kyxscms.com/) 


## 主要新特性

* 支持小说自动采集，一分钟采集一万本
* 运行速度更快，毫秒级返回
* 删除了插件、评论、用户管理
* 模板是可以用的，自己放入到view目录下即可
* APP已开发完成，如有需要联系邮箱 909996473@qq.com


## 部署

* 需要修改 .env 文件

* 导入 public/qfxscms.sql 文件到数据库 
  
* composer install
  

* 根目录运行 
~~~
  php think swoole
~~~

* 自动爬取小说
~~~
  后台》数据采集》采集管理》编辑某个采集规则》自动采集勾选即可
~~~

* swoole配置

  > 自行参考官网配置，需要爬取速度更快修改 config/swoole.php my_queue.worker_num

* nginx设置
  
  
~~~
    location  ~* \.(gif|png|jpg|jpeg|css|js|woff|woff2|txt|svg)$
    {
        root /www/wwwroot/qfxscms;
    }
    location /public/{
        root /www/wwwroot/qfxscms;
    }
    location /uploads/{
        root /www/wwwroot/qfxscms/public;
    }

    location ^~ /
    {
        proxy_pass http://127.0.0.1:8083;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header REMOTE-HOST $remote_addr;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection $connection_upgrade;
        add_header X-Cache $upstream_cache_status;
        set $static_file7HUXuqc6 0;
        if ( $uri ~* "\.(gif|png|jpg|css|js|woff|woff2)$" )
        {
            set $static_file7HUXuqc6 1;
            expires 12h;
        }
        if ( $static_file7HUXuqc6 = 0 )
        {
        add_header Cache-Control no-cache;
        }
    }
  
~~~

## 参与开发

任何人都可参与该仓库开发

## 版权信息

禁止任何人使用本代码商用与销售
