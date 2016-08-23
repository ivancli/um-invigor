<?php

/**
 * This file is part of UM,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Invigor\UM
 */

return [

    /* change the following configuration if necessary */
    'users_table' => 'users',
    'roles_table' => 'roles',
    'permissions_table' => 'permissions',
    'permission_role_table' => 'permission_role',
    'role_user_table' => 'role_user',
    'groups_table' => 'groups',
    'group_user_table' => 'group_user',

    'user_foreign_key' => 'user_id',
    'role_foreign_key' => 'role_id',
    'permission_foreign_key' => 'permission_id',
    'group_foreign_key' => 'group_id',

    'role' => 'Invigor\UM\UMRole',
    'permission' => 'Invigor\UM\UMPermission',
    'group' => 'Invigor\UM\UMGroup',
    'user' => 'App\User',

    'user_controller' => 'UserController',
    'group_controller' => 'GroupController',
    'role_controller' => 'RoleController',
    'permission_controller' => 'PermissionController',

    'bootstrap_css_path' => asset('um/bootstrap/css/bootstrap.min.css'),




    /* view routes */
    'route_user' => 'um::'
];
