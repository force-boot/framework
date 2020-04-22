KiloPHP- 基于PHP7的简单快速的轻量级php框架
===============
## 概要
* 完全开源，使用MIT开源协议
* 采用`PHP7`强类型（严格模式）
* 符合`PSR4`自动加载规范和`PSR-2`命名规范
* 支持`Composer`管理
* 支持`ORM` 使用 `Laravel Eloquent ORM`组件
* 基于`FastRoute`实现路由，快速灵活
* 使用`Twig`作为框架模板引擎，语法简单，易学习，功能强大
* 独立多应用支持
* 表单令牌，session，cookie管理，缓存，安全过滤等
* 易扩展，代码规范，注释，文档齐全
* 轻量，快速，利于学习
* 更多参见文档和代码，该项目还在更新维护中

> 运行环境要求PHP7.0+。
> 
## 版本更新
## v1.0 
第一个正式版本发布
## v1.1
1. 优化了单数据库和多数据库配置项定义
2. 增加Twig模板`request`全局变量，`server`,`method`,`cookie`等
3. 增加Twig模板`config`配置全局变量，可以获取全局配置和独立应用配置
```
//假设 获取home主配置的子配置domain
{{app.config.home.domian}} //显示127.0.0.1
//获取当前请求类型
{{app.request.method}}
//获取当前域名（带协议）
{{app.request.domain}}
//当前是否手机访问
{{app.request.isMoblie}}
//更多请查看文档
```
4. 基础控制器增加request属性用于获取`request`类对象，和`app`属性获取当前访问的应用名称
5. 优化了几处细节，修正几处注释
6. 更多...
## 安装
~~~
composer create-project kilo/kilophp kilo
~~~

## 文档

https://www.kancloud.cn/xiejiawei/kilophp

## 命名规范
`kilophp`遵循PSR-2命名规范和PSR-4自动加载规范。