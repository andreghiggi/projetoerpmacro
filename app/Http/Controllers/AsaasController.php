<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AsaasController extends Controller
{
    public function index(Request $request){
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://api.asaas.com/v3/pix/qrCodes/static', [
            'body' => '{"value":1}',
            'headers' => [
                'accept' => 'application/json',
                'access_token' => '$aact_MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OjJlNTQ4OWQ2LWM0ODYtNGRkMy1iYjI5LTA3MTk3Mzk5Y2VkODo6JGFhY2hfMDNmYTQwYTQtNmZiNi00MmU0LWFjMTEtYTEwNDg4ZjYzOGVi',
                'content-type' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody(),true);
        echo '<img src="data:image/jpeg;base64,'.($data['encodedImage']).'">';

        echo "<br>Payload: " . $data['payload'];
    }
}
