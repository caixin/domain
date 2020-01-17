<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes...
Route::get('login', 'Admin\AdminLoginController@showLoginForm')->name('login');
Route::post('login', 'Admin\AdminLoginController@login')->name('loginaction');
//AJAX
Route::post('ajax/topinfo', 'AjaxController@getTopInfo')->name('ajax.topinfo');
Route::post('ajax/perpage', 'AjaxController@setPerPage')->name('ajax.perpage');
Route::post('ajax/imageupload/{dir}', 'AjaxController@imageUpload')->name('ajax.imageupload');

Route::group(['middleware'=>['auth','navdata','permission','share']], function () {
    Route::get('/', 'HomeController@index')->name('index');
    Route::post('/', 'HomeController@updateHealth')->name('update_index');
    Route::get('logout', 'Admin\AdminLoginController@logout')->name('logout');

    Route::resource('admin', 'Admin\AdminController', ['only'=>['index','create','store','edit','update','destroy']]);
    Route::post('admin/search', 'Admin\AdminController@search')->name('admin.search');
    Route::post('admin/{admin}/save', 'Admin\AdminController@save')->name('admin.save');
    Route::get('admin/editpwd', 'Admin\AdminController@editpwd')->name('admin.editpwd');
    Route::post('admin/updatepwd', 'Admin\AdminController@updatepwd')->name('admin.updatepwd');

    Route::resource('admin_role', 'Admin\AdminRoleController', ['only'=>['index','create','store','edit','update','destroy']]);
    Route::post('admin_role/search', 'Admin\AdminRoleController@search')->name('admin_role.search');
    Route::post('admin_role/{admin}/save', 'Admin\AdminRoleController@save')->name('admin_role.save');

    Route::resource('admin_nav', 'Admin\AdminNavController', ['only'=>['index','create','store','edit','update','destroy']]);
    Route::post('admin_nav/search', 'Admin\AdminNavController@search')->name('admin_nav.search');
    Route::post('admin_nav/{admin_nav}/save', 'Admin\AdminNavController@save')->name('admin_nav.save');

    Route::get('admin_login_log', 'Admin\AdminLoginLogController@index')->name('admin_login_log.index');
    Route::post('admin_login_log/search', 'Admin\AdminLoginLogController@search')->name('admin_login_log.search');

    Route::get('admin_action_log', 'Admin\AdminActionLogController@index')->name('admin_action_log.index');
    Route::post('admin_action_log/search', 'Admin\AdminActionLogController@search')->name('admin_action_log.search');

    Route::resource('node', 'Domain\NodeController', ['only'=>['index','create','store','edit','update','destroy']]);
    Route::post('node/search', 'Domain\NodeController@search')->name('node.search');
    Route::resource('domain_group', 'Domain\DomainGroupController', ['only'=>['index','create','store','edit','update','destroy']]);
    Route::post('domain_group/search', 'Domain\DomainGroupController@search')->name('domain_group.search');
    Route::post('domain_group/hash', 'Domain\DomainGroupController@hash')->name('domain_group.hash');
    Route::resource('domain', 'Domain\DomainController', ['only'=>['index','create','store','edit','update','destroy']]);
    Route::post('domain/search', 'Domain\DomainController@search')->name('domain.search');
    Route::get('domain/export', 'Domain\DomainController@export')->name('domain.export');
    Route::post('domain/import', 'Domain\DomainController@import')->name('domain.import');

    Route::get('manual', 'Domain\ManualController@index')->name('manual.index');
    Route::post('manual', 'Domain\ManualController@action')->name('manual.action');
    Route::resource('detect', 'Domain\DetectController', ['only'=>['index','edit','update']]);
    Route::post('detect/search', 'Domain\DetectController@search')->name('detect.search');
    Route::post('detect/{detect}/save', 'Domain\DetectController@save')->name('detect.save');
    Route::get('detect_log', 'Domain\DetectLogController@index')->name('detect_log.index');
    Route::post('detect_log/search', 'Domain\DetectLogController@search')->name('detect_log.search');
    Route::get('jump_log', 'Domain\JumpLogController@index')->name('jump_log.index');
    Route::post('jump_log/search', 'Domain\JumpLogController@search')->name('jump_log.search');
});
