<?php

use think\Route;
use think\Config;

Route::miss('v3/Index/miss');
Route::get('/', 'v3/Index/index');

if (Config::get('debug_module')) {
    // Debug
    Route::group('debug', function() {
        Route::get('index', 'debug/Index/index');
        Route::get('logout', 'debug/Index/logout');
        Route::get('login', 'v3/User/login');
        Route::get('user', 'v3/User/getUserInfo');
        Route::get('report/:id', 'debug/Index/getReport');
        Route::get('report', 'debug/Index/getReportList');
        Route::miss('debug/Index/index');
    });
}

// API
Route::group('api', function() {
    // API v3
    Route::group('v3', function() {
        // 登录模块
        Route::get('user/callback', 'v3/User/loginCallback'); // 这个需要写在下一行前面，否则无法生效
        Route::get('user', 'v3/User/getUserInfo');
        Route::post('user', 'v3/User/login');
        Route::delete('user', 'v3/User/logout');

        // 公共信息
        Route::get('board', 'v3/Publics/getPublicBoard');
        Route::get('places', 'v3/Publics/getPublicPlaces');
        Route::get('resources', 'v3/Publics/getPublicResources');

        // 申请模块
        Route::get('apply', 'v3/Apply/getUserApplies');
        Route::post('apply', 'v3/Apply/createApply');
        Route::delete('apply/:applyid', 'v3/Apply/deleteApply');
        Route::put('apply', 'v3/Apply/updateApply');

        // 管理员部分
        Route::group('admin', function() {
            // 审查模块
            Route::get('resources', 'v3/Check/getPermittedResources');
            Route::get('applies', 'v3/Check/getPermittedApplies');
            Route::put('apply', 'v3/Check/check');

            // 历史模块
            Route::get('history', 'v3/History/getHistory');
        });

        // 超级管理员部分
        Route::group('superadmin', function() {
            // 历史模块
            Route::get('history', 'v3/History/queryHistory');

            // 权限模块
            Route::post('user', 'v3/Auth/addUser');
            Route::delete('user/:userid', 'v3/Auth/removeUser');
            Route::put('user', 'v3/Auth/changeUserAuth');
            Route::get('user', 'v3/Auth/queryUser');

            // 场所模块
            Route::post('place', 'v3/Place/addPlace');
            Route::delete('place/:placeid', 'v3/Place/removePlace');
            Route::put('place', 'v3/Place/updatePlace');
            Route::get('place', 'v3/Place/getPlaceInfo');

            // 资源模块
            Route::post('resource', 'v3/Resource/addResource');
            Route::delete('resource/:resourceid', 'v3/Resource/removeResource');
            Route::put('resource', 'v3/Resource/updateResource');
            Route::get('resource', 'v3/Resource/getResourceInfo');

            // 关联表模块
            Route::post('relation', 'v3/Relation/addRelation');
            Route::delete('relation/:adminid/:resourceid', 'v3/Relation/removeRelation');
            Route::get('relation', 'v3/Relation/getRelation');

            // 错误报告模块
            Route::get('report/:id', 'v3/Report/getReport');
            Route::get('report', 'v3/Report/list');

            // 通知模块
            Route::post('notification', 'v3/Notification/addNotification');
            Route::delete('notification/:notificationid', 'v3/Notification/removeNotification');
            Route::put('notification', 'v3/Notification/updateNotification');
            Route::get('notification', 'v3/Notification/getNotificationInfo');
        });
    });
});