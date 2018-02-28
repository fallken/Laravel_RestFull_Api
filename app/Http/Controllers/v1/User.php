<?php

namespace App\Http\Controllers\v1;;

use App\Services\UserService;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use App\Http\Controllers\Controller;


class User extends Controller
{
    protected $user;
    protected $mail;
    public function __construct(UserService $service)
    {
        $this->user=$service;
       $this->mail = new PHPMailer(true);
        //need to specify pics path
    }

    public function Register(){
        $main=new PHPMailer();
    $parms=[];
    $parms=request()->input();
    if (isset($parms['username'])&&isset($parms['email'])&&isset($parms['pw'])&&isset($parms['name'])&&!empty($parms['username'])&&
    !empty($parms['email'])&&!empty($parms['pw'])){
        if ($this->user->isRegisteredUser($parms['username']))
            return $this->jsonify(0,0,10);
        if (!$this->user->CheckRequest($parms['email'], 'isEmail'))
            return $this->jsonify(0,0,11);
        if ($this->user->isRegisteredEmail($parms['email']))
            return $this->jsonify(0,0,12);
        $addUser=$this->user->addUser($parms['username'],$parms['email'],$parms['pw'],$parms['name']);
        if($addUser){
            ///here we will add our email library
            $email_template =  view("email.email");
            $mail_activate_content = view("email.main_active");

            $mail_activate_content = str_replace(["%USERNAME%", "%CODE%"], [$parms['name'], $addUser['activation_code']], $mail_activate_content);

            $email_template = str_replace("%CONTENT%", $mail_activate_content, $email_template);

            $this->sendMail('arsalani@outlook.com', $email_template, "تایید رایانامه");

            return $this->jsonify(1,$addUser,0);
        }
        else
            return $this->jsonify(0,0,13);
    }else
        return $this->jsonify(0,0,25);
}
    public function Login(){
        $parms=[];
        $parms=request()->input();
        if (isset($parms['username'])&&isset($parms['pw'])&&!empty($parms['username'])&&!empty($parms['pw'])){
                if ($this->user->isRegisteredUser($parms['username'])){
                    $login=$this->user->login($parms['username'],$parms['pw']);
                    if ($login)
                        return $this->jsonify(1,$login,0);
                    else
                        return $this->jsonify(0,0,16);
                }else
                    return $this->jsonify(0,0,15);
        }else
            return $this->jsonify(0,0,14);
    }
    public function Details(){
        $parms=[];
        $parms=request()->input();
        if (isset($parms['token'])&&!empty($parms['token'])){
            $token=$this->user->getUserFromToken($parms['token']);
            if ($token)
                return $this->jsonify(1,$token,0);
            else
                return $this->jsonify(0,0,17);
        }else
            return $this->jsonify(0,0,18);
    }
    public function LogOut(){
        $parms=[];
        $parms=request()->input();
        if ($this->user->isValidToken($parms['token'])){
            if ($this->user->deleteToken($parms['token']))
                return $this->jsonify(1,0,19);
            else
                return $this->jsonify(0,0,20);
        }
        else
            return $this->jsonify(0,0,21);
    }
    public function ForgetPassword(){
        $parms=[];
        $parms=request()->input();
        if (isset($parms['email'])&&!empty($parms['email'])){
            if (!$this->user->CheckRequest($parms['email']))
                return $this->jsonify(0,0,11);
            if (!$this->user->isRegisteredEmail($parms['email']))
                return $this->jsonify(0,0,22);//user does not exist
            if (!$this->user->isActivatedEmail($parms['email']))
                return $this->jsonify(0,0,23);//user is not activated
            $forgot_pw_hash=$this->user->rand_code(10);//generating random code for changing password
            $updateUser=$this->user->updateForgotPass($forgot_pw_hash,$parms['email']);

            $email_template = view("email.email");
            $forget_pw_content = view("email.forget_pw");

            $forget_pw_content = str_replace("%CODE%", $forgot_pw_hash, $forget_pw_content);

            $email_template = str_replace("%CONTENT%", $forget_pw_content, $email_template);

            $this->sendMail($parms['email'], $email_template, "فراموشی رمز عبور");

            //Send output
            return $this->jsonify(2,'forgot password email has been successfully sent ',0);
        }
        else
            return $this->jsonify(0,0,23);///email field shouldnt be empty
    }
    public function ForgetPasswordProcess(){
     $parms=[];
     $parms=request()->input();
     if (isset($parms['v'])&&!empty($parms['v'])){
         if ($userInfo=$this->user->CheckForgetPwHash($parms['v'])){
             $newPw=$this->user->randomPassword();
             if ($this->user->updateNewPassword($userInfo->id,$newPw[1])){
                 $email_template=view('email.email');
                 $forget_pw_content=view('email.new_pw');
               	$new_pw_template = str_replace(["%USERNAME%", "%CODE%"], [$userInfo->name, $newPw[0]], $forget_pw_content);
                $email_template = str_replace("%CONTENT%", $new_pw_template, $email_template);
                 $this->sendMail($userInfo['email'], $email_template, "رمز عبور جدید");
                 exit("<script>alert('رمز عبور جدید با موفقیت ایجاد و به رایانامه شما ارسال گردید.');location.replace('http://hifitapp.ir');</script>");
             }
             else
                 exit("<script>alert('خطایی هنگام به روزرسانی اطلاعات دیتابیس رخ داده است.');location.replace('http://hifitapp.ir');</script>");
         }
         else
             exit("<script>alert('کد فعال سازی نامعتبر است.');location.replace('http://hifitapp.ir');</script>");
     }
     else
         header("Location: http://hifitapp.ir");

             }
    public function emailVerify(){
        $parms=[];
        $parms=request()->input();
        if( isset($parms['v']) )
        {



            if($this->user->CheckActivationCode($parms['v']))
            {
                if($this->user->isActivate_Code($parms['v']))
                    exit("<script>alert('رایانامه شما با موفقیت تایید شد.');location.replace('http://hifitapp.ir');</script>");
                else
                    exit("<script>alert('خطایی هنگام به روزرسانی اطلاعات دیتابیس رخ داده است.');location.replace('http://hifitapp.ir');</script>");
            }
            else
                exit("<script>alert('کد فعال سازی نامعتبر است.');location.replace('http://hifitapp.ir');</script>");
        }
        else
            header("Location: http://hifitapp.ir");
    }
    public function test(){
        $parms=[];
        $parms=request()->input();
    if ($userDetails=$this->user->getUserInfo(651)){
        $userDetails->pw == $parms['old_pw'];
        return $this->jsonify(2,'parameters match like charm',0);//its totally working
    }
    else{
        return false;
    }
    }
    public function editProfile(){
        $parms=[];
        $parms=request()->input();
        if (isset($parms['token'])&&!empty($parms['token'])){
            if ($userId=$this->user->isValidToken($parms['token'])){
                $data=[];
                if (isset($parms['name'])&&!empty($parms['name']))
                    $data['name']=$parms['name'];

                if (isset($parms['tel'])&&!empty($parms['tel'])){
                    $search = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹");
                    $search2 = array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");
                    $replace = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
                    $parms['tel'] = str_replace($search, $replace, $parms['tel']);
                   $parms['tel'] = str_replace($search2, $replace, $parms['tel']);

                    if (strlen($parms['tel']) == 11 && is_numeric($parms['tel']))
                        $data["tel"] = $parms['tel'];
                    else
                        return $this->jsonify(2, 'something wrong with tel', 0);
                }
                if (isset($parms['gender'])&&!empty($parms['gender']))
                    if (is_numeric($parms['gender']))
                        $data['gender']=$parms['gender'];
                else
                    return $this->jsonify(2,'something was wrong with gender',0);
                if (isset($parms['age'])&&!empty($parms['age']))
                    if (is_numeric($parms['age']))
                        $data['age']=$parms['age'];
                    else
                        return $this->jsonify(2,'something was wrong with age',0);
                if (isset($parms['height'])&&!empty($parms['height']))
                    if (is_numeric($parms['height']))
                        $data['height']=$parms['height'];
                    else
                        return $this->jsonify(2,'something was wrong with height',0);
                if (isset($parms['weight'])&&!empty($parms['weight']))
                    if (is_numeric($parms['weight']))
                        $data['weight']=$parms['weight'];
                    else
                        return $this->jsonify(2,'something was wrong with weight',0);
                if (isset($parms['blood_type'])&&!empty($parms['blood_type']))
                    if (is_numeric($parms['blood_type']))
                        $data['blood_type']=$parms['blood_type'];
                    else
                        return $this->jsonify(2,'something was wrong with blood_type',0);

                    $updateUser=$this->user->updateUserInfo($data,$userId);
                    if ($updateUser)
                        return $this->jsonify(2,'operation was successfull',0);
                    else
                        return $this->jsonify(2,'updating process was not successfull',0);
            }
            return $this->jsonify(2,'the input token is not valid',0);
        }
        return $this->jsonify(2,'token shouldnt be empty');
    }
    public function changePassword(){
        $parms=[];
        $parms=request()->input();
            if (isset($parms['token'])&&!empty($parms['token'])&&isset($parms['old_pw'])&&!empty($parms['old_pw'])&&
                isset($parms['new_pw'])&&!empty($parms['new_pw'])){
            if ($userId=$this->user->isValidToken($parms['token'])) {
                $userInfo = $this->user->getUserInfo($userId);

                if ($userInfo->pw == urlencode($parms['old_pw'])) {
                    $data = [];
                    $data['pw'] = $parms['new_pw'];
                    if ($this->user->updateUserInfo($data, $userId))
                        return $this->jsonify(2, 'password chaged successfully', 0);
                    else
                        return $this->jsonify(2, 'there was a problem updating new password', 0);
                } else
                    return $this->jsonify(2,'user creditentials not matching ',0);

            }
            else
                return $this->jsonify(2, 'input token not valid', 0);
        }
        else
            return $this->jsonify(2,'parameters not set',0);
    }
    public function RemovePhoto(){
        $parms=[];
        $parms=request()->input();
        if (isset($parms['token'])&&!empty($parms['token'])){
            if ($userId=$this->user->isValidToken($parms['token']))
             $userInfo=$this->user->getUserInfo($userId);
            if ($userInfo->pic != 'default'){
                unlink(ProfilePic_PATH . $userInfo->pic);//need to test and specify the path
                $update=$this->user->updateUserInfo(['pic'=>'default'],$userId);
                if ($update)
                    return $this->jsonify(2,'image deleted successfully');
                else
                    return $this->jsonify(2,'there was a problem updating user profile');//it doesnt need to pass the third argument it would be set on 0 on default
            }
            return $this->jsonify(2,'user does not have any pick');
        }
        return $this->jsonify(2,'parameters not set correctly');
    }
    public function UpdatePhoto(){
        $parms=[];
        $parms=request()->input();
        if (isset($parms['token'])&&!empty($parms['token'])&&isset($_FILES['photo']['name'])
            &&!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] == 0){
            $file = pathinfo($_FILES['photo']['name']);
            if ($file['extension'] != "jpg")
                return $this->jsonify(2, "the select files format is not valid");

            if ($user_id = $this->user->isValidToken($parms['token'])) {
                $user_data = $this->user->getUserInfo($user_id);
                $new_name = $this->user->rand_code(5) . $user_id . ".jpg";
                if (move_uploaded_file($_FILES['photo']['tmp_name'], ProfilePic_PATH . $new_name)) {
                    if ($user_data->pic != "default")
                        unlink(ProfilePic_PATH . $user_data->pic);
                    $updateUser=$this->user->updateUserInfo(['pic'=>$new_name],$user_id);
                    if ($updateUser)
                        return $this->jsonify(2,['photo_name'=>$new_name]);
                    else
                        return $this->jsonify(2,'user was not updated');
                }
                else
                    return $this->jsonify(2,'could not upload the image');
        }
        return $this->jsonify(2,'token is not valid');
        }
        return $this->jsonify(2,'input arguments are not valid');
    }





    ///////////////////////////////

    protected function jsonify($stat=0,$data=0,$errNo=null){
        //a function to create better structured json response
        $err= app('ErrorGen');//this will load the class in the services foler in App directory named ErrorGenerator
        return $err->errorMaker($stat,$data,$errNo);

    }
    protected function sendMail($to_email, $html_content, $subject){
        date_default_timezone_set('Asia/Tehran');

        $mail = new PHPMailer;

        $mail->CharSet = 'UTF-8';

        //Tell PHPMailer to use SMTP
        $mail->isSMTP();

        $mail->SMTPDebug = 0;

        $mail->Debugoutput = 'html';

        $mail->Host = 'server64.mylittledatacenter.com';

        $mail->Port = 465;

        $mail->SMTPSecure = 'ssl';

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->SMTPAuth = true;

        $mail->Username = "noreply@hifitapp.ir";

        $mail->Password = "5]5Mo%^TAq[D";

        //Set who the message is to be sent from
        $mail->setFrom('noreply@hifitapp.ir', 'های فیت');

        //Set who the message is to be sent to
        $mail->addAddress($to_email, 'Dear User');

        //Set the subject line
        $mail->Subject = '🏋️ های فیت : '.$subject;

        $mail->msgHTML($html_content);

        $mail->AltBody = 'این ایمیل جهت فعال سازی رایانامه ارسال شده است.';

        //send the message, check for errors
        if (!$mail->send()) {
            return false;
            //echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            return true;
        }
    }

}
