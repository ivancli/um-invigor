<?php namespace Invigor\UM\Contracts;

/**
 * This file is part of UM,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Invigor\UM
 */

interface UMPermissionInterface
{
    
    /**
     * Many-to-Many relations with role model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles();

    /**
     * Many-to-One relations with permission model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentPerm();

    /**
     * One-to-Many relations with permission model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childPerms();
}
