<?php

namespace App\Http\Controllers;

use App\Services\TestService;
use App\Token;
use App\User;
use Hamcrest\Thingy;
use Illuminate\Http\Request;

class Test extends Controller
{
    //lets first make a service container and add it to the controller then we will move on and try one of the requests in former api and add
    //use  it one here and see the changes
    protected $test;//define a protected variable
    public function __construct(TestService $service)
    {
        $this->test=$service;

    }

    public function index(){

            $parms=request()->input();
            $data=$this->test->getPost($parms['id']);
            if ($data) {
                return $this->jsonify(1,$data,0);
            }
            else {
                return $this->jsonify(0,0,3);
            }

    }
    public function like(){
        try{
            $parms=request()->input();
        $likes=$this->test->addLike($parms['id']);
        return response()->json(['ok'=>true,'likes'=>$likes]);}
        catch (Exception $e){
        return response()->json(['ok'=>false,'message'=>'error happened during trying to get information from database'],500);
        }

    }
    public function disLike(){
        try{
            $parms=request()->input();
            $likes=$this->test->disLike($parms['id']);
            return response()->json(['ok'=>true,'likes'=>$likes]);}
        catch (Exception $e){
            return response()->json(['ok'=>false,'message'=>'error happened during trying to get information from database'],500);
        }
    }
    public function cats(){
        try{
            $cats=$this->test->cats();
            return response()->json(['ok'=>true,'likes'=>$cats]);}
        catch (Exception $e){
            return response()->json(['ok'=>false,'message'=>'error happened during trying to get information from database'],500);
        }
    }
    public function test(){
//        $parms=[];
//        $parms=request()->input();
//        $tokenId= Token::where('token',$parms['token'])->get();
////        $user=Token::find($tokenId)->user;
//
//        return response()->json($tokenId);
//        $parms=[];
//        $parms=request()->input();
//        $tokenId= User::where('id',$parms['id'])->first();
//        $user=Token::find($tokenId)->user;
        $err= app('ErrorGen');
        return response()->json($err->index());
    }
    public function getComments(){//ill work on its security later not now . for now i will just define the functions and take tests from them
        try{
            $parms=[];
            $parms=request()->input();
            $data=$this->test->getComments($parms['id'],$parms['offset']);
            return response()->json(['ok'=>true,'comments'=>$data]);}
        catch (Exception $e){
            return response()->json(['ok'=>false,'message'=>'error happened during trying to get information from database'],500);
        }

    }
    public function addComment(){
        $parms=[];
        $parms=request()->input();
        $targetId=isset($parms['target_id'])?$parms['target_id']:0;
        $data=$this->test->addComment($parms['user_id'],$parms['post_id'],$parms['comment_body'],$targetId);
        return response()->json(['ok'=>true,'comments'=>$data]);
}
    public function Search(){//i think i should be using ordinary strucrured data errors like the one used on
        //need to create another service container for handling errors and requests
        ///or ill pass the data and the error name to jsonifier and then the jsonifier will use the sevice container to get the error data and the will retun the error structure to me
        $parms=[];
        $parms=request()->input();
        $parms['word']=urldecode($parms['word']);
        $parms['word']=urlencode($parms['word']);
        $word=isset($parms['word'])?$parms['word']:null;
        $posts=$this->test->searchPost($word);
            //count the number of comments
        if ($posts){
            return  response()->json(['ok'=>true,'result'=>$posts]);
        }
            else{
                return  response()->json(['ok'=>false,'result'=>'the'],27);//this->errorLoger(27);
        }


    }

    protected function jsonify($stat=0,$data=0,$errNo=null){
        //a function to create better structured json response
        $err= app('ErrorGen');
        return $err->errorMaker($stat,$data,$errNo);

    }

}
