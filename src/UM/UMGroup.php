<?php
/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/18/2016
 * Time: 5:19 PM
 */

namespace Invigor\UM;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Invigor\UM\Contracts\UMGroupInterface;
use Invigor\UM\Traits\UMGroupTrait;

class UMGroup extends Model implements UMGroupInterface
{
    use UMGroupTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    protected $fillable = ['name', 'active', 'url', 'description'];

    protected $appends = array('urls');
    /**
     * Creates a new instance of the model.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Config::get('um.groups_table');
    }
}