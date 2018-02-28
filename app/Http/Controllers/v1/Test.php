<?php

namespace App\Http\Controllers\v1;

use App\Services\TestService;
use App\Token;
use App\User;
use App\Post;
use Hamcrest\Thingy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;




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
                return $this->jsonify(0,0,1);
            }

    }
    public function like(){
            $parms=request()->input();
        $likes=$this->test->addLike($parms['id']);
        if ($likes){
            return $this->jsonify(1,$likes,0);
        }
        else {
            return $this->jsonify(0,0,3);
        }
    
    }
    public function disLike(){

            $parms=request()->input();
            $likes=$this->test->disLike($parms['id']);
            if ($likes){
                return $this->jsonify(1,$likes,0);
            }
        else{
            return $this->jsonify(0,0,3);
            }
    }
    public function cats(){

            $cats=$this->test->cats();
            if ($cats) {
                return $this->jsonify(1, $cats, 0);
            }
            else{
                return $this->jsonify(0, 0, 3);
            }

    }
    public function test(){
       var_dump($this->ConvertArrayToString($this->test->cats())) ;
    }
    public function getComments(){//ill work on its security later not now . for now i will just define the functions and take tests from them

            $parms=[];
            $parms=request()->input();
            $data=$this->test->getComments($parms['id'],$parms['offset']);
            if ($data){
                return $this->jsonify(1, $data, 0);
            }
            else{
                return $this->jsonify(0, 0, 1);
            }


    }
    public function addComment(){
        $parms=[];
        $parms=request()->input();
        $targetId=isset($parms['target_id'])?$parms['target_id']:0;
        $user_id=$this->test->getUserFromToken($parms['token']);
        $data=$this->test->addComment($parms['token'],$parms['post_id'],$parms['comment_body'],$targetId);
        if ($data){
            $this->push($parms['target_id'],$parms['comment_body'],$user_id);
            $this->jsonify(1,$data,0);
        }
        else{
            $this->jsonify(0,0,3);
        }
    }

    public function Search(){//i think i should be using ordinary strucrured data errors like the one used on
        //need to create another service container for handling errors and requests
        ///or ill pass the data and the error name to jsonifier and then the jsonifier will use the sevice container to get the error data and the will retun the error structure to me
        //i will add the offset section later.its fine
        $parms=[];
        $parms=request()->input();
        $word=isset($parms['word'])?$parms['word']:null;
        if (!is_null($word)){
            if (strlen($word)>=4){ //count the number of comments
                $posts = $this->test->searchPost($word);

                if ($posts) {
                    for ($i=0;$i<count($posts);$i++){
                        $posts[$i]['comments']=$this->test->getCommentsNum($posts[$i]['id']);
                    }
                    return $this->jsonify(1, $posts, 0);
                } else {
                    return $this->jsonify(0, 0, 9);
                }
            }else{
                return $this->jsonify(0,0,5);
            }
        }
        return $this->jsonify(0,0,8);
    }
    public function TopNewPosts(){//in close future i will also add the check if the cat id exists in the db and will generate responses according to that
        $parms=[];
        $parms=request()->input();
        $catId=null;
        $catId=(isset($parms['CatId'])&&is_numeric($parms['CatId'])&&!empty($parms['CatId']))?$parms['CatId']:null;
        $posts=$this->test->TopNewPosts($catId);
        for ($i=0;$i<count($posts);$i++){
            $posts[$i]['comments']=$this->test->getCommentsNum($posts[$i]['post_id']);
        }
        if ($posts){
            return $this->jsonify(1,$posts,0);
        }else
            return $this->jsonify(0,0,2);
    }
    public function MainPage(){//need to define a getCommentsNum function in testService
        $data['cats']=$this->test->cats();
        $data['slide_main']=$this->test->Slides();
        $data['most_viewed']=$this->test->TopNewPosts();
        $data['new_posts']=$this->test->NewPosts();
        for ($i=0;$i<count($data['most_viewed']);$i++){
            $data['most_viewed'][$i]['comments']=$this->test->getCommentsNum($data['most_viewed'][$i]['post_id']);
        }
        return $this->jsonify(1,$data,0);
    }

    /////list of local functions
    protected function jsonify($stat=0,$data=0,$errNo=null){
        //a function to create better structured json response
        $err= app('ErrorGen');//this will load the class in the services foler in App directory named ErrorGenerator
        return $err->errorMaker($stat,$data,$errNo);

    }
    ///adding my custome pushing function
    private function push($targetId , $commentBody, $senderId){
        $userId= $this->test->getUserIdFromCommentId($targetId);
        $senderName=$this->test->getUser($senderId);
        $image='';
        if ($senderName->pic != 'default'):
            $image = 'http://hifitapp.ir/aaaa/p/'.$senderName->pic;
        else:
            $image='http://hifitapp.ir/aaaa/p/default.png';
        endif;

        $content = array(
            "en" => base64_decode($commentBody)
        );
        $heading=array(
            "en"=> $senderName->name
        );
        $fields = array(
            'app_id' => "fe76a49c-c607-47f8-b986-c44bffe9fa2d",
            "filters"=> [
                [ "field"=> "tag", "key"=> "user_id", "relation"=> "=", "value"=> $userId]
            ],
            'large_icon'=>$image,//for image i wonder if i should use responders image or a  default image . i wonder
            'contents' => $content,
            'small_icon'=>'http://hifitapp.ir/images/hifitapp.png',
            'headings'=>$heading
        );

        $fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ZGRiNmZjNGEtNGQyNS00Y2NiLWI5YjgtN2ExNzA0YzI5Mzhm'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;


    }

}
