<?php
use \kilophp\Route;

//添加一个GET方式路由
Route::get('/book/{id:\d+}','home/book/show');
//添加一个POST方式路由
Route::post('/book/{id:\d+}','home/book/add');
//添加一个PUT方式路由
Route::put('/book/{id:\d+}','home/book/edit');
//添加一个delete方式路由
Route::delete('/book/{id:\d+}','home/book/delete');
//添加一个HEAD方式路由
Route::head('/book/{id:\d+}','home/book/index');
//添加一个PATCH方式路由
Route::patch('/book/{id:\d+}','home/book/index');

//批量添加PUT方式路由
Route::add('PUT', [
    '/user/{id:\d+}' => 'home/index/index',
    '/list' => 'home/index/list',
]);

//批量添加post路由
Route::post([
    '/user/{id:\d+}' => 'home/index/index',
    '/list' => 'home/index/list',
]);

//批量添加GET路由
Route::get([
    '/user/{id:[1-9]}' => 'home/index/index',
    '/list' => 'home/index/list',
]);

//使用分组定义GET方式路由
Route::group('/admin',[
    '/do' => 'admin/index/do',// /admin/do
    '/user' => 'admin/index/user' // admin/user
],'GET');
