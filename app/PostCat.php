<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $img
 */
//it was a  test to check if i could add models to specific folder then start programming so it worked and it was awsome just
//needed to add --output-path=\mypath
class PostCat extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'img'];


    public function posts(){
        return $this->hasMany('App\Post','cat_id');
    }
}
