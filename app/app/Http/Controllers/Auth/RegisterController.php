<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Account;
use App\Http\Controllers\Controller;
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

        return Validator::make($data, (new User)->rules());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {

        //Se crea la cuenta de la empresa
          $account= Account::create([
            'email' => $data['email'],
            'name' => $data['name'],
        ]);
              
   
    //se adiciona el usuario administrador con la cuenta relacionada
       $user = User::create([
            'email' => $data['email'],
             'password' => bcrypt($data['password']),
            'account_id'=>$account->id,
        ]);
        return $user;
    }
}
