<?php
Route::group(['middleware' => ['web']], function () {
    Route::get('um/home', function () {
        return view('um::index');
    });
    Route::group(['middleware' => ['auth']], function () {
        Route::resource('um/user', 'App\Http\Controllers\UM\UserController');
        Route::resource('um/group', 'App\Http\Controllers\UM\GroupController');
        Route::resource('um/role', 'App\Http\Controllers\UM\RoleController');
        Route::resource('um/permission', 'App\Http\Controllers\UM\PermissionController');

        Route::get('um/logout', 'Invigor\UM\Controllers\UMAuthController@logout');
    });

    Route::group(['middleware' => ['guest']], function () {
        Route::get('um/login', function () {
            return view("um::auths.login");
        });
        Route::post('um/login', 'Invigor\UM\Controllers\UMAuthController@postLogin');
    });
});