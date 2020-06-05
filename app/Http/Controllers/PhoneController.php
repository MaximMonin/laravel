<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Notifications\AccountVerification;
use Notification;

class PhoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function sendsms ()
    {
        $response = array();
        $phone = request ('smsphone');
    
        $userId = Auth::user()->id;  //Getting UserID.
        if($userId == "" || $userId == null){
            $response['message'] = 'You are logged out, Login again.';
        }
        else {
          User::where('id', $userId)->update(['phone' => $phone]);
        }

        if ( isset($phone) && $phone=="" ) {
            $response['message'] = 'Invalid phone number';
        } else {  
            $otp = rand(100000, 999999);

            $notObject = new AccountVerification('Code: ' . strval($otp));
            $result = Notification::route('turbosms', '+' . $phone)->notify($notObject);
// dd ($result);
   
            Session::put('OTP', $otp);  
            Session::put('PHONE', $phone);  
            $response['message'] = 'Your OPT is created.';
            return redirect('profile')->with('status', __('messages.SmsSent') . ' ' . $phone);
        }
        return redirect('profile')->with('status', __('messages.SmsNotSent'));
//        echo json_encode($response);
    }
    public function verify(Request $request)
    {
       $response = array();
    
       $enteredOtp = request ('smscode');
       $userId = Auth::user()->id;  //Getting UserID.
    
        if($userId == "" || $userId == null){
            $response['message'] = 'You are logged out, Login again.';
        }else{
            $phone = $request->session()->get('PHONE');
            $OTP = $request->session()->get('OTP');
            if(strval($OTP) === strval($enteredOtp)){
   
                User::where('id', $userId)->update(['phone_verified_at' => now(), 'phone' => $phone]);
    
                //Removing Session variable
                Session::forget('PHONE');
                Session::forget('OTP');
    
                $response['message'] = "Your Number is Verified.";
                return redirect('profile')->with('status', __('messages.PhoneVerified') );
            }else{
                $response['message'] = "OTP does not match.";
            }
        }
        return redirect('profile')->with('status', __('messages.PhoneNotVerified') );
//        echo json_encode($response);
    }
}
