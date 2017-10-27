<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $var
 * @property string $val1
 * @property string $val2
 * @property string $link
 * @property string $type
 */
class Main extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'main';

    /**
     * @var array
     */
    protected $fillable = ['var', 'val1', 'val2', 'link', 'type'];

}
