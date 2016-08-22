<?php namespace Invigor\UM;

/**
 * This file is part of UM,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Invigor\UM
 */

use Invigor\UM\Contracts\UMPermissionInterface;
use Invigor\UM\Traits\UMPermissionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class UMPermission extends Model implements UMPermissionInterface
{
    use UMPermissionTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    protected $fillable = ['name', 'display_name', 'description'];

    /**
     * Creates a new instance of the model.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Config::get('um.permissions_table');
    }

}
