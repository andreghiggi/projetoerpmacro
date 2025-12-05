<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\ConfiguracaoSuper;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $config = ConfiguracaoSuper::first();
        if($config && $config->auto_cadastro == 0){
            die;
        }
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
        return Validator::make($data, 
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'captcha' => request()->captchaa == 1 ? ['required', 'captcha'] : []
            ],
            [
                'password.min' => 'minímo de 6 caracteres',
                'password.confirmed' => 'senhas não coencidem',
                'email.unique' => 'email já utilizado',
                'captcha.required' => 'Digite os caracteres',
                'captcha.captcha' => 'Caracteres incorretos',
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'plano_auto_cadastro' => $data['plano'] ?? null
        ]);

        if($data['email'] == env("MAILMASTER")){
            $user->assignRole('gestor_plataforma');
        }else{
            $user->assignRole('admin');
        }
        return $user;
    }
}
