<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\CategoriaNuvemShop;

class NuvemShopUtil
{

	public function create($request, $produto){
		$store_info = session('store_info');
		$api = new \TiendaNube\API($store_info['store_id'], $store_info['access_token'], 'Awesome App ('.$store_info['email'].')');

		$dataProduto = [
			'name' => $produto->nome,
			'description' => $request->texto_nuvem_shop

		];
		if($request->categoria_nuvem_shop){
			$dataProduto['categories'] = [$request->categoria_nuvem_shop];
		}
		$response = $api->post("products", $dataProduto);
		$prod = $response->body;

		$produto->nuvem_shop_id = $prod->id;
		$produto->save();

		$response = $api->put("products/$prod->id/variants/".$prod->variants[0]->id, [
			'price' => __convert_value_bd($request->nuvem_shop_valor),
			'stock' => __convert_value_bd($request->estoque_inicial),
			'promotional_price' => __convert_value_bd($request->nuvem_shop_valor_promocional),
			'barcode' => $request->codigo_barras,
			"weight" => $request->peso_nuvem_shop,
			"width" => $request->largura_nuvem_shop,
			"height" => $request->altura_nuvem_shop,
			"depth" => $request->comprimento_nuvem_shop,
		]);

		if ($request->hasFile('image')) {

			$image = base64_encode(file_get_contents(public_path($produto->img)));

			$ext = $request->file('image')->getClientOriginalExtension();
			$response = $api->post("products/$prod->id/images",[
				"filename" => Str::random(20).".".$ext,
				"attachment" => $image
			]);
		}

		return $response;
	}
}