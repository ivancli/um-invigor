<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/18/2016
 * Time: 5:26 PM
 */

namespace Invigor\UM\Traits;


use Illuminate\Support\Facades\Config;

trait UMGroupTrait
{
    /**
     * Many-to-Many relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(Config::get('um.user'), Config::get('um.group_user_table'), Config::get('um.group_foreign_key'), Config::get('um.user_foreign_key'));
    }

    public function getUrlsAttribute()
    {
        return array(
            "show" => route("um.group.show", $this->id),
            "edit" => route("um.group.edit", $this->id),
            "delete" => route("um.group.destroy", $this->id),
        );
    }
    /**
     * Attach a user to current group.
     *
     * @param $user
     * @return void
     *
     */
    public function attachUsers($user)
    {
        if (is_object($user)) {
            $user = $user->getKey();
        }

        if (is_array($user)) {
            $user = $user['id'];
        }

        $this->users()->attach($user);
    }

    /**
     * Detach a user form current group.
     *
     * @param $user
     * @return void
     *
     */
    public function detachUsers($user)
    {
        if (is_object($user))
            $user = $user->getKey();

        if (is_array($user))
            $user = $user['id'];

        $this->users()->detach($user);
    }


    /**
     * Boot the group model
     * Attach event listener to remove the many-to-many records when trying to delete
     * Will NOT delete any records if the group model uses soft deletes.
     *
     * @return void|bool
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($groups) {
            if (!method_exists(Config::get('um.group'), 'bootSoftDeletes')) {
                $groups->users()->sync([]);
            }

            return true;
        });
    }
}