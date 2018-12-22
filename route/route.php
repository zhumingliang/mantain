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
Route::get('api/:version/token/user', 'api/:version.Token/getWXToken');

Route::rule('api/:version/access/save', 'api/:version.AccessControl/save');
Route::get('api/:version/access/list', 'api/:version.AccessControl/getList');
Route::get('api/:version/access/export', 'api/:version.AccessControl/export');
Route::get('api/:version/access', 'api/:version.AccessControl/getTheAccess');


Route::post('api/:version/meeting/save', 'api/:version.Meeting/save');
Route::get('api/:version/meeting', 'api/:version.Meeting/getMeetingInfo');
Route::post('api/:version/meeting/update', 'api/:version.Meeting/update');
Route::post('api/:version/meeting/delete', 'api/:version.Meeting/delete');
Route::post('api/:version/meeting/sign/in', 'api/:version.Meeting/signIn');
Route::get('api/:version/meeting/list', 'api/:version.Meeting/getMeetingList');
Route::get('api/:version/meeting/sign/in/list', 'api/:version.Meeting/getSignInList');
Route::get('api/:version/meeting/export', 'api/:version.Meeting/exportMeeting');
Route::get('api/:version/meeting/sign/in/export', 'api/:version.Meeting/exportSignIn');
Route::get('api/:version/meeting/info', 'api/:version.Meeting/getInfoForMC');
Route::get('api/:version/meeting/sign/in/list/wx', 'api/:version.Meeting/getSignInListForWx');

Route::post('api/:version/meeting/place/save', 'api/:version.MeetingPlace/save');
Route::get('api/:version/meeting/place/list', 'api/:version.MeetingPlace/getList');
Route::get('api/:version/meeting/place/export', 'api/:version.MeetingPlace/export');
Route::get('api/:version/meeting/place', 'api/:version.MeetingPlace/getMeetingPlace');

Route::post('api/:version/meeting/recept/save', 'api/:version.MeetingRecept/save');
Route::get('api/:version/meeting/recept/list', 'api/:version.MeetingRecept/getList');
Route::get('api/:version/meeting/recept/export', 'api/:version.MeetingRecept/export');
Route::get('api/:version/meeting/recept', 'api/:version.MeetingRecept/getMeetingRecept');


Route::post('api/:version/multi/save', 'api/:version.Multi/save');
Route::get('api/:version/multi/list', 'api/:version.Multi/getList');
Route::get('api/:version/multi/export', 'api/:version.Multi/export');

Route::post('api/:version/official/save', 'api/:version.Official/save');
Route::get('api/:version/official/list', 'api/:version.Official/getList');
Route::get('api/:version/official/export', 'api/:version.Official/export');
Route::get('api/:version/official', 'api/:version.Official/getOfficial');
Route::get('api/:version/official/meal/address', 'api/:version.Official/getAddress');


Route::post('api/:version/car/save', 'api/:version.Car/save');
Route::get('api/:version/car/list', 'api/:version.Car/getList');
Route::get('api/:version/car/export', 'api/:version.Car/export');


Route::post('api/:version/recreational/save', 'api/:version.Recreational/save');
Route::get('api/:version/recreational/list', 'api/:version.Recreational/getList');
Route::get('api/:version/recreational/export', 'api/:version.Recreational/export');


Route::post('api/:version/flow/check/pass', 'api/:version.Flow/checkPass');
Route::get('api/:version/flow/info', 'api/:version.Flow/getInfo');
Route::get('api/:version/flow/complete', 'api/:version.Flow/getComplete');

Route::get('api/:version/meeting/rooms', 'api/:version.MeetingRoom/getRooms');
Route::get('api/:version/meeting/room/check', 'api/:version.MeetingRoom/checkRooms');

Route::get('api/:version/department/list', 'api/:version.Department/getList');

Route::get('api/:version/admin/list', 'api/:version.Admin/getList');
Route::get('api/:version/admins/access', 'api/:version.Admin/getListForAccess');
Route::get('api/:version/role/list', 'api/:version.Admin/getRoleList');
Route::post('api/:version/admin/save', 'api/:version.Admin/save');
Route::post('api/:version/admin/role/update', 'api/:version.Admin/updateRole');
Route::post('api/:version/admin/post/update', 'api/:version.Admin/updatePost');


Route::post('api/:version/category/save', 'api/:version.Category/save');
Route::post('api/:version/category/handel', 'api/:version.Category/handel');
Route::post('api/:version/category/update', 'api/:version.Category/update');
Route::get('api/:version/category/list', 'api/:version.Category/getList');
Route::get('api/:version/category', 'api/:version.Category/getTheCategory');
