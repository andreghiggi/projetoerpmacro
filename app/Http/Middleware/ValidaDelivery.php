<?php

namespace App\Http\Middleware;

use App\Models\MarketPlaceConfig;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ValidaDelivery
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

        $item = MarketPlaceConfig::where('loja_id', $request->link)
        ->first();

        if($item == null){
            abort(404);
        }

        if($item->status == 0){
            abort(404);
        }

        $request->merge(['loja_id' => $item->id]);
        return $next($request);

    }
}
