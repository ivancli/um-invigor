<?php namespace Invigor\UM\Traits;

/**
 * This file is part of UM,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Invigor\UM
 */

use Illuminate\Support\Facades\Config;

trait UMPermissionTrait
{
    /**
     * Many-to-Many relations with role model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Config::get('um.role'), Config::get('um.permission_role_table'), Config::get('um.permission_foreign_key'), Config::get('um.role_foreign_key'));
    }

    public function parentPerm()
    {
        return $this->belongsTo(Config::get('um.permission'), 'parent_id', 'id');
    }

    public function childPerms()
    {
        return $this->hasMany(Config::get('um.permission'), 'parent_id', 'id');
    }

    /**
     * Boot the permission model
     * Attach event listener to remove the many-to-many records when trying to delete
     * Will NOT delete any records if the permission model uses soft deletes.
     *
     * @return void|bool
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($permission) {
            if (!method_exists(Config::get('um.permission'), 'bootSoftDeletes')) {
                $permission->roles()->sync([]);
            }

            return true;
        });
    }
}
