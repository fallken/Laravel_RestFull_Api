<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $img
 */
class PostCat extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'img'];

}
