<?php

namespace App\Http\Controllers\API\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketPlaceConfig;
use App\Models\CupomDesconto;
use App\Models\Cliente;
use App\Models\CupomDescontoCliente;
use App\Models\BairroDelivery;
use App\Models\FuncionamentoDelivery;
use App\Models\TamanhoPizza;

class ConfigController extends Controller
{
   public function index(){
      $item = MarketPlaceConfig::
      where('empresa_id', request()->empresa_id)
      ->with('cidade')
      ->first();

      $dia = date('w');
      $hora = date('H:i');
      $dia = FuncionamentoDelivery::getDia($dia);
      $funcionamento = FuncionamentoDelivery::where('dia', $dia)
      ->where('empresa_id', request()->empresa_id)->first();
      $aberto = false;

      if($funcionamento != null){
         $item->fim_expediente = $funcionamento->fim;
         $item->inicio_expediente = $funcionamento->inicio;

         $atual = strtotime(date('Y-m-d H:i'));
         $dataHoje = date('Y-m-d');
         $inicio = strtotime($dataHoje . " " . $funcionamento->inicio);
         $fim = strtotime($dataHoje . " " . $funcionamento->fim);
         if($atual > $inicio && $atual < $fim){
            $aberto = true;
         }
      }else{
         $item->fim_expediente = null;
         $item->inicio_expediente = null;
      }
      

      $item->aberto = $aberto;
      $item->tipo_entrega = json_decode($item->tipo_entrega);

      $tiposPagamento = [];
      if($item != null){
         $tipos_pagamento = $item->tipos_pagamento ? json_decode($item->tipos_pagamento) : [];

         foreach($tipos_pagamento as $tp){
            // array_push($tiposPagamento, MarketPlaceConfig::tiposPagamento()[$tp]);
            array_push($tiposPagamento, $tp);
         }
         $item->tipos_pagamento = $tiposPagamento;
      }
      $item->maximo_sabores_pizza = 0;

      $tamanho = TamanhoPizza::where('empresa_id', request()->empresa_id)
      ->orderBy('maximo_sabores', 'desc')->first();
      if($tamanho != null){
         $item->maximo_sabores_pizza = $tamanho->maximo_sabores;
      }

      return response()->json($item, 200);
   }

   public function cupom(Request $request){
      $item = CupomDesconto::where('codigo', $request->cupom)
      ->where('empresa_id', $request->empresa_id)
      ->where('status', 1)
      ->first();
      if($item == null){
         return response()->json("cupom não encontrado", 404);
      }
      $total = $request->total;

      if($total < $item->valor_minimo_pedido){
         return response()->json("valor minímo para este cupom R$ " . __moeda($item->valor_minimo_pedido), 401);
      }

      $cliente = Cliente::where('uid', $request->uid)->first();
      if($cliente == null){
         return response()->json("cliente não encontrado", 404);
      }

      $cupomUsuado = CupomDescontoCliente::where('empresa_id', $request->empresa_id)
      ->where('cupom_id', $item->id)
      ->where('cliente_id', $cliente->id)
      ->first();
      if($cupomUsuado != null){
         return response()->json("cupom já utilizado no pedido #$cupomUsuado->pedido_id", 404);
      }

      $desconto = 0;
      if($item->tipo_desconto == 'valor'){
         $desconto = $item->valor;
      }else{
         $desconto = $total * ($item->valor/100);
      }
      
      return response()->json($desconto, 200);

   }

   public function bairros(Request $request){
      $data = BairroDelivery::
      where('empresa_id', $request->empresa_id)
      ->where('status', 1)
      ->get();

      return response()->json($data, 200);

   }
}
