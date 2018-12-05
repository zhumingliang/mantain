<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');
Route::get('api/:version/token/admin', 'api/:version.Token/getAdminToken');
Route::get('api/:version/token/login/out', 'api/:version.Token/loginOut');

Route::rule('api/:version/access/save', 'api/:version.AccessControl/save');
Route::get('api/:version/access/list', 'api/:version.AccessControl/getList');

Route::post('api/:version/meeting/save', 'api/:version.Meeting/save');
Route::post('api/:version/meeting/update', 'api/:version.Meeting/update');
Route::post('api/:version/meeting/delete', 'api/:version.Meeting/delete');

Route::post('api/:version/meeting/place/save', 'api/:version.MeetingPlace/save');


Route::post('api/:version/recreational/save', 'api/:version.Recreational/save');
Route::get('api/:version/recreational/list', 'api/:version.Recreational/getList');
