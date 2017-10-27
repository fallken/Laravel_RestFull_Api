<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $email
 * @property string $mobile_details
 * @property string $text
 * @property string $name
 */
class Bug extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['email', 'mobile_details', 'text', 'name'];

}
