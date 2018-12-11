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
Route::get('api/:version/access/export', 'api/:version.AccessControl/export');
Route::get('api/:version/access', 'api/:version.AccessControl/getTheAccess');


Route::post('api/:version/meeting/save', 'api/:version.Meeting/save');
Route::post('api/:version/meeting/update', 'api/:version.Meeting/update');
Route::post('api/:version/meeting/delete', 'api/:version.Meeting/delete');
Route::post('api/:version/meeting/sign/in', 'api/:version.Meeting/signIn');
Route::get('api/:version/meeting/list', 'api/:version.Meeting/getMeetingList');
Route::get('api/:version/meeting/sign/in/list', 'api/:version.Meeting/getSignInList');
Route::get('api/:version/meeting/export', 'api/:version.Meeting/exportMeeting');
Route::get('api/:version/meeting/sign/in/export', 'api/:version.Meeting/exportSignIn');

Route::post('api/:version/meeting/place/save', 'api/:version.MeetingPlace/save');
Route::get('api/:version/meeting/place/list', 'api/:version.MeetingPlace/getList');
Route::get('api/:version/meeting/place/export', 'api/:version.MeetingPlace/export');

Route::post('api/:version/meeting/recept/save', 'api/:version.MeetingRecept/save');
Route::get('api/:version/meeting/recept/list', 'api/:version.MeetingRecept/getList');
Route::get('api/:version/meeting/recept/export', 'api/:version.MeetingRecept/export');


Route::post('api/:version/multi/save', 'api/:version.Multi/save');
Route::get('api/:version/multi/list', 'api/:version.Multi/getList');
Route::get('api/:version/multi/export', 'api/:version.Multi/export');

Route::post('api/:version/official/save', 'api/:version.Official/save');
Route::get('api/:version/official/list', 'api/:version.Official/getList');
Route::get('api/:version/official/export', 'api/:version.Official/export');

Route::post('api/:version/car/save', 'api/:version.Car/save');
Route::get('api/:version/car/list', 'api/:version.Car/getList');
Route::get('api/:version/car/export', 'api/:version.Car/export');


Route::post('api/:version/recreational/save', 'api/:version.Recreational/save');
Route::get('api/:version/recreational/list', 'api/:version.Recreational/getList');
Route::get('api/:version/recreational/export', 'api/:version.Recreational/export');


Route::post('api/:version/flow/check', 'api/:version.Flow/check');
Route::get('api/:version/flow/info', 'api/:version.Flow/getInfo');
