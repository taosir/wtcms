# wtcms
**wtcms** 是基于ThinkPHP框架的一套CMS系统，方便用户快速建立企业网站，门户网站，个人博客或其他系统的内容管理系统。<br>
# 主页
![home_view](https://github.com/taosir/wtcms/blob/master/public/images/home.png)<br>
# 功能特色
- 基于ueditor的内容编辑器；<br>
- 基于bootstrap的前端框架开发；<br>
- 文章搜索功能；<br>
- 内容自定义灵活；<br>
- 基于thinkphp和thinkcmf，内容精简，二次开发方便。<br>
# 运行环境<br>
- 操作系统：Windows/Mac/Linux <br>
- php：>= 5.4<br>
- mysql：>= 5.5<br>
- rewrite on <br>
# 安装部署<br>
1、配置好`LAMP/WAMP`环境<br>
  2、新建一个数据库`storage`<br>
  3、将`wtcms.sql`文件内容导入`storage`数据库中<br>
  4、将整个项目文件放入到`WWW`文件夹下<br>
  5、修改`/data/conf/db.php`<br>
```php
return array(
    'DB_TYPE' => 'mysqli', 
    'DB_HOST' => 'localhost', //数据库ip
    'DB_NAME' => 'storage', //数据库名
    'DB_USER' => 'root', //数据库用户名
    'DB_PWD'  => 'root', //数据库密码
    'DB_PORT' => '3306', //数据库端口号
    'DB_PREFIX' => 'wt_', //数据表前缀
    'AUTHCODE'  => 'AdvwqCzyPFsdSweDsd', //密钥
    'COOKIE_PREFIX' => 'wt_', //cookies前缀
);
```
# 目录结构  
```php
wtcms 根目录
|-- admin   后台文件夹
|-- application  应用目录
|    |-- Admin    管理员模块
|    |-- Api      手机客户端API模块
|    |-- Asset    Ueditor模块
|    |-- Comment  文章评论模块
|    |-- Common   通用基础模块
|    |-- Home     主模块
|    |-- Install  安装程序模块        
|    |-- Portal   门户应用模块 
|-- data    数据目录
|    |-- conf        动态配置目录
|    |    |-- db.php  数据库配置文件
|    |    |-- config.php 全局配置文件
|    |    |...
|    |-- runtime     应用的运行时目录(可写)
|    |-- upload      上传文件存储路径
|    |...
|-- plugins 插件目录
|    |-- comment     评论插件目录
|-- public  公共静态文件存放
|-- themes  主题目录
|    |-- default     默认主题
|    |    |-- Comment 评论页面
|    |    |-- Portal  模板页面
|    |    |-- Public  公共模板
|    |    |-- User    用户页面模板
|-- thinkphp    thinkphp核心文件夹
|-- index.php   入口文件
|-- wtcms.sql   数据文件
``` 
# 后台管理 <br>
- 登录地址：'/admin' <br>
- 登录账号: `admin` <br>
- 登录密码为: `123456` <br>
# 感谢<br>
- Bootstrap<br>
- ThinkPHP<br>
- Thinkcmf<br>
# 效果演示
- http://storage.hust.edu.cn/

