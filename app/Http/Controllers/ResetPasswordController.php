<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Mail;

class ResetPasswordController extends Controller
{
    public function reset(Request $request){
        $user = User::where('email', $request->email)->first();
        if($user == null){
            return back()->with('error', 'Email não encontrado');
        }

        $newPassoword = Str::random(4);

        $user->password = Hash::make($newPassoword);
        $user->save();

        $teste = Mail::send('mail.nova_senha', ['newPassoword' => $newPassoword, 'name' => $user->name ], function($m) use ($user){

            $nomeEmail = env('MAIL_FROM_NAME');
            $m->from(env('MAIL_USERNAME'), $nomeEmail);
            $m->subject('recuperação de senha');
            $m->to($user->email);
        });
        return redirect()->route('login')->with('success', 'Foi enviado o email com uma nova senha');

    }
}
