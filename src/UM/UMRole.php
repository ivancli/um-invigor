<?php namespace Invigor\UM;

/**
 * This file is part of UM,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Invigor\UM
 */

use Invigor\UM\Contracts\UMRoleInterface;
use Invigor\UM\Traits\UMRoleTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class UMRole extends Model implements UMRoleInterface
{
    use UMRoleTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    protected $fillable = ['name', 'display_name', 'description'];

    protected $appends = ['urls'];
    /**
     * Creates a new instance of the model.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Config::get('um.roles_table');
    }
}
