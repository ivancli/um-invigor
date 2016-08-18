<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/18/2016
 * Time: 5:21 PM
 */

namespace Invigor\UM\Contracts;


interface UMGroupInterface
{
    /**
     * Many-to-Many relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();


    /**
     * Attach a user to current group.
     *
     * @param $user
     * @return void
     *
     */
    public function attachUsers($user);

    /**
     * Detach a user form current group.
     *
     * @param $user
     * @return void
     *
     */
    public function detachUsers($user);

}