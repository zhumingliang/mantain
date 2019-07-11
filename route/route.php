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
Route::get('cms/index', 'index/index');
Route::get('api/:version/token/admin', 'api/:version.Token/getAdminToken');
Route::get('api/:version/token/login/out', 'api/:version.Token/loginOut');
Route::get('api/:version/token/user', 'api/:version.Token/getWXToken');
Route::get('api/:version/token/verify', 'api/:version.Token/verify');
Route::get('api/:version/token/verify/check', 'api/:version.Token/captchaCheck');

Route::rule('api/:version/access/save', 'api/:version.AccessControl/save');
Route::get('api/:version/access/list', 'api/:version.AccessControl/getList');
Route::get('api/:version/access/export', 'api/:version.AccessControl/export');
Route::get('api/:version/access', 'api/:version.AccessControl/getTheAccess');


Route::post('api/:version/meeting/save', 'api/:version.Meeting/save');
Route::get('api/:version/meeting', 'api/:version.Meeting/getMeetingInfo');
Route::get('api/:version/meeting/push', 'api/:version.Meeting/checkMeetingPush');
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
Route::post('api/:version/flow/check/pass/repair', 'api/:version.Flow/checkPassForRepair');
Route::get('api/:version/flow/info', 'api/:version.Flow/getInfo');
Route::get('api/:version/flow/complete', 'api/:version.Flow/getComplete');
Route::get('api/:version/flow/ready', 'api/:version.Flow/getReady');

Route::get('api/:version/meeting/rooms', 'api/:version.MeetingRoom/getRooms');
Route::get('api/:version/meeting/room/check', 'api/:version.MeetingRoom/checkRooms');

Route::get('api/:version/department/list', 'api/:version.Department/getList');
Route::get('api/:version/departments', 'api/:version.Department/getDepartments');

Route::get('api/:version/admin/list', 'api/:version.Admin/getList');
Route::get('api/:version/admins/access', 'api/:version.Admin/getListForAccess');
Route::get('api/:version/role/list', 'api/:version.Admin/getRoleList');
Route::post('api/:version/admin/save', 'api/:version.Admin/save');
Route::post('api/:version/admin/role/update', 'api/:version.Admin/updateRole');
Route::post('api/:version/admin/post/update', 'api/:version.Admin/updatePost');
Route::post('api/:version/admin/department/update', 'api/:version.Admin/updateDepartment');
Route::post('api/:version/admin/department/bind', 'api/:version.Admin/bindDepartment');


Route::post('api/:version/category/save', 'api/:version.Category/save');
Route::post('api/:version/category/handel', 'api/:version.Category/handel');
Route::post('api/:version/category/update', 'api/:version.Category/update');
Route::get('api/:version/category/list', 'api/:version.Category/getList');
Route::get('api/:version/category', 'api/:version.Category/getTheCategory');

Route::post('api/:version/unit/save', 'api/:version.Unit/save');
Route::get('api/:version/unit/list', 'api/:version.Unit/getList');

Route::post('api/:version/buffet/save', 'api/:version.Buffet/save');
Route::get('api/:version/buffet/list', 'api/:version.Buffet/getList');
Route::get('api/:version/buffet/export', 'api/:version.Buffet/export');

Route::post('api/:version/hotel/save', 'api/:version.Hotel/save');
Route::get('api/:version/hotel/list', 'api/:version.Hotel/getList');
Route::get('api/:version/hotel/export', 'api/:version.Hotel/export');

Route::post('api/:version/sku/save', 'api/:version.Sku/save');
Route::get('api/:version/sku/nav', 'api/:version.Sku/getNav');
Route::post('api/:version/sku/upload', 'api/:version.Sku/upload');
Route::post('api/:version/stock/upload', 'api/:version.Sku/uploadExcelToSaveStock');
Route::post('api/:version/sku/update', 'api/:version.Sku/update');
Route::post('api/:version/sku/image/handel', 'api/:version.Sku/SkuImageHandel');
Route::get('api/:version/sku', 'api/:version.Sku/getTheSku');
Route::get('api/:version/sku/list', 'api/:version.Sku/getSkuList');
Route::get('api/:version/sku/list/use', 'api/:version.Sku/getSkuForUse');
Route::get('api/:version/sku/apply/list', 'api/:version.Sku/getListForApply');
Route::get('api/:version/sku/apply/detail', 'api/:version.Sku/applyDetail');
Route::get('api/:version/sku/apply/export', 'api/:version.Sku/exportApply');
Route::post('api/:version/collar/use/save', 'api/:version.Sku/collarUseSave');

Route::get('api/:version/sku/stock/list', 'api/:version.Sku/getSkuStockList');
Route::get('api/:version/stock/list', 'api/:version.Sku/getStockList');
Route::post('api/:version/stock/save', 'api/:version.Sku/stockSave');
Route::post('api/:version/stock/handel', 'api/:version.Sku/stockHandel');
Route::get('api/:version/stock/export', 'api/:version.Sku/exportStock');

Route::post('api/:version/repair/save', 'api/:version.Repair/save');
Route::get('api/:version/repair/check/info', 'api/:version.Repair/getInfo');
Route::get('api/:version/repair/list', 'api/:version.Repair/getList');
Route::get('api/:version/repair/export', 'api/:version.Repair/export');
Route::get('api/:version/repair/image', 'api/:version.Repair/getImage');


Route::post('api/:version/image/upload', 'api/:version.Image/upload');

Route::post('api/:version/index', 'api/:version.Index/index');

Route::get('/', 'api/Index/index');






