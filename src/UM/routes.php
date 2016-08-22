<?php
Route::resource('um/user', 'App\Http\Controllers\UM\UserController');
Route::resource('um/group', 'App\Http\Controllers\UM\GroupController');
Route::resource('um/role', 'App\Http\Controllers\UM\RoleController');
Route::resource('um/permission', 'App\Http\Controllers\UM\PermissionController');
Route::get('um/login', function(){
    return view("um::auths.login");
});
Route::post('um/login', 'App\Http\Controllers\Auth\AuthController@postLogin');
