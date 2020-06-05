<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show()
    {
        return view('profile', Auth::user());
    }

    public function update(Request $request)
    {
      $user = User::find (request ('id'));
      $user->name = request('name');
      if ($user->email !== request('email'))
      {
         $user->email_verified_at = null;
      }
      $user->email = request('email');
      if ($user->phone !== request('phone'))
      {
         $user->phone_verified_at = null;
      }
      $user->phone = request('phone');
      if (request('password') !== null )
      {
        $user->password = Hash::make(request('password'));
        $rules = array(
          'name'             => 'required',                        // just a normal required validation
          'phone'            => 'required',                        // just a normal required validation
          'password'         => 'required|string|min:8|confirmed',
          'password-confirm' => 'same:password'                    // has to match the password field
        );
      }
      else {
        $rules = array(
          'name'             => 'required',                        // just a normal required validation
          'phone'            => 'required',                        // just a normal required validation
        );
      }

      // do the validation ----------------------------------
      // validate against the inputs from our form
      $validator = Validator::make($request->all(), $rules);

      // check if the validator failed -----------------------
      if ($validator->fails()) {
        // dd( $validator->messages());
        // redirect our user back to the form with the errors from the validator
        return redirect (route ('profile'))->withErrors($validator);
      }
      $user->save();
      return redirect (route ('home'));
    }
    //
}
