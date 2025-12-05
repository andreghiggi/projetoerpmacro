<?php

namespace App\Http\Middleware;

use App\Models\ConfiguracaoCardapio;
use App\Models\Mesa;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ValidaCardapio
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next){
        if(!isset($request->link)){
            abort(403);
        }

        if(env("CARDAPIO_QRCODE") == 0){
            abort(403, 'Acesso não permitido!');
        }

        $item = Mesa::where('hash', $request->link)
        ->first();

        if($item == null){
            abort(403, 'Mesa não encontrada!');
        }

        if($item->status == 0){
            abort(403, 'Mesa desativada!');
        }

        $config = ConfiguracaoCardapio::where('empresa_id', $item->empresa_id)->first();
        if($config == null){
            session()->flash("flash_error", 'Primeiro faça a configuração');
            return redirect()->route('config-cardapio.index');
        }

        if($config->qr_code_mesa == 0){
            abort(403, 'QR Cárdapio não está ativo');
        }

        $request->merge(['config_id' => $config->id]);
        return $next($request);
    }
}
