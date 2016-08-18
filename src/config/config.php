<?php

/**
 * This file is part of UM,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Invigor\UM
 */

return [

    /*
    |--------------------------------------------------------------------------
    | UM Role Model
    |--------------------------------------------------------------------------
    |
    | This is the Role model used by UM to create correct relations.  Update
    | the role if it is in a different namespace.
    |
    */
    'role' => 'App\Role',

    /*
    |--------------------------------------------------------------------------
    | UM Roles Table
    |--------------------------------------------------------------------------
    |
    | This is the roles table used by UM to save roles to the database.
    |
    */
    'roles_table' => 'roles',

    /*
    |--------------------------------------------------------------------------
    | UM Permission Model
    |--------------------------------------------------------------------------
    |
    | This is the Permission model used by UM to create correct relations.
    | Update the permission if it is in a different namespace.
    |
    */
    'permission' => 'App\Permission',

    /*
    |--------------------------------------------------------------------------
    | UM Permissions Table
    |--------------------------------------------------------------------------
    |
    | This is the permissions table used by UM to save permissions to the
    | database.
    |
    */
    'permissions_table' => 'permissions',

    /*
    |--------------------------------------------------------------------------
    | UM permission_role Table
    |--------------------------------------------------------------------------
    |
    | This is the permission_role table used by UM to save relationship
    | between permissions and roles to the database.
    |
    */
    'permission_role_table' => 'permission_role',

    /*
    |--------------------------------------------------------------------------
    | UM role_user Table
    |--------------------------------------------------------------------------
    |
    | This is the role_user table used by UM to save assigned roles to the
    | database.
    |
    */
    'role_user_table' => 'role_user',

    /*
    |--------------------------------------------------------------------------
    | User Foreign key on UM's role_user Table (Pivot)
    |--------------------------------------------------------------------------
    */
    'user_foreign_key' => 'user_id',

    /*
    |--------------------------------------------------------------------------
    | Role Foreign key on UM's role_user Table (Pivot)
    |--------------------------------------------------------------------------
    */
    'role_foreign_key' => 'role_id',

    /*
    |--------------------------------------------------------------------------
    | UM Group Model
    |--------------------------------------------------------------------------
    |
    | This is the Group model used by UM to create correct relations.
    | Update the group if it is in a different namespace.
    |
    */
    'group' => 'App\Group',

    /*
    |--------------------------------------------------------------------------
    | UM Groups Table
    |--------------------------------------------------------------------------
    |
    | This is the groups table used by UM to save groups to the
    | database.
    |
    */
    'groups_table' => 'groups',

    'group_user_table' => 'group_user',
];
