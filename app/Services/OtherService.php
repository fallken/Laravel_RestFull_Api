<?php
/**
 * Created by PhpStorm.
 * User: Elomir
 * Date: 11/11/2017
 * Time: 7:10 PM
 */

namespace App\Services;


use App\Bug;

class OtherService
{
    public function addBug($name,$email,$mobileDetails,$text){
      $addBug=  Bug::insert(['name'=>$name,'email'=>$email,'mobile_details'=>$mobileDetails,'text'=>$text]);
      if ($addBug)
          return true;
      else
          return false;
    }
}