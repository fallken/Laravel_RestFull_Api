<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $code
 * @property boolean $activated
 */
class Activation extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'type', 'code', 'activated'];

}
