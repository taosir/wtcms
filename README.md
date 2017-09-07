# wtcms<br>
** <font color=#0099ff>wtcms</font> ** 是基于ThinkPHP框架的一套CMS系统，方便用户快速建立企业网站，门户网站，个人博客或其他系统的内容管理系统。<br>

# 首页<br>
![home_view](https://github.com/taosir/wtcms/blob/master/data/home.png)<br>

# 功能特色<br>
- 基于ueditor的内容编辑器；<br>
- 基于bootstrap的前端框架开发；<br>
- 文章搜索功能；<br>
- 内容自定义灵活；<br>
- 基于thinkphp和thinkcmf，内容精简，二次开发方便。<br>

# 运行环境<br>
- 操作系统：Windows/Mac/Linux <br>
- php：>= 5.4<br>
- mysql：>= 5.5<br>

# 安装部署<br>
  1、配置好LAMP/WAMP环境<br>
  2、新建一个数据库storage<br>
  3、将wtcms.sql文件内容导入storage数据库中<br>
  4、将整个项目文件放入到WWW文件夹下<br>
  5、修改./data/conf/db.php<br>
```php
<?php
/**
 * 配置文件
 */
return array(
    'DB_TYPE' => 'mysqli', 
    'DB_HOST' => 'localhost', //数据库ip
    'DB_NAME' => 'storage', //数据库名
    'DB_USER' => 'root', //数据库用户名
    'DB_PWD' => 'root', //数据库密码
    'DB_PORT' => '3306', //数据库端口号
    'DB_PREFIX' => 'wt_', //数据表前缀
    "AUTHCODE" => 'AdvwqCzyPFsdSweDsd', //密钥
    "COOKIE_PREFIX" => 'wt_', //cookies前缀
);
```
# 感谢<br>
- Bootstrap<br>
- ThinkPHP<br>
- Thinkcmf<br>

