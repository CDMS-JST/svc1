<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'PREKEY' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'givnname' => ['required', 'string', 'max:255'],
            'lastkana' => ['required', 'string', 'max:255'],
            'givnkana' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        if($data['PREKEY']!==config('const.PREKEY')){
            header('Location: ./');
            exit;
        }
        return User::create([
            'lastname' => $data['lastname'],
            'givnname' => $data['givnname'],
            'lastkana' => $data['lastkana'],
            'givnkana' => $data['givnkana'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'isadmin' => '1'
        ]);
    }
}