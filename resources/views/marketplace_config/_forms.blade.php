<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('descricao', 'Descrição')
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('rua', 'Rua')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('numero', 'Número')->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('bairro', 'Bairro')->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::select('cidade_id', 'Cidade')
        ->required()
        ->options($item != null ? [$item->cidade_id => $item->cidade->info] : [])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('cep', 'CEP')
        ->attrs(['class' => 'cep'])
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('telefone', 'WhatsApp')
        ->attrs(['class' => 'fone'])
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::tel('email', 'Email')
        ->required()
        ->type('email')
        !!}
    </div>

    <hr>
    <h5>Redes Sociais</h5>
    <div class="col-md-4">
        {!!Form::text('link_instagram', 'Link do instagram')
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::text('link_facebook', 'Link do facebook')
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::text('link_whatsapp', 'Link do whatsApp')
        !!}
    </div>

    <hr>
    <h5>Dados de entrega</h5>
    <div class="col-md-2">
        {!!Form::text('latitude', 'Latitude')
        ->attrs(['class' => 'coordenada'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('longitude', 'Longitude')
        ->attrs(['class' => 'coordenada'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('pedido_minimo', 'Valor de pedido mínimo')
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->pedido_minimo) : '')
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('valor_entrega', 'Valor para entrega padrão')
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->valor_entrega) : '')
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('valor_entrega_gratis', 'Valor para entrega gratis')
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->valor_entrega_gratis) : '')
        !!}
    </div>
    <!-- <div class="col-md-2">
        {!!Form::select('usar_bairros', 'Utilizar bairros', [1 => 'Sim', 0 => 'Não'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div> -->

    <div class="col-lg-3 col-12">
        <label for="">Tipo de entrega</label>
        <select required class="select2 form-control select2-multiple" name="tipo_entrega[]" data-toggle="select2" multiple="multiple" id="tipo_entrega">
            <option @if(in_array('balcao', (isset($item) ? $item->tipo_entrega : []))) selected @endif value="balcao">Balcão</option>
            <option @if(in_array('delivery', (isset($item) ? $item->tipo_entrega : []))) selected @endif value="delivery">Delivery</option>
        </select>
    </div>

    <hr>

    <div class="col-lg-2 col-12">
        <label for="">Tipo (segmento)</label>
        <select class="select2 form-control select2-multiple" name="segmento[]" data-toggle="select2" multiple="multiple" id="segmento">
            <option @if(in_array('produtos', (isset($item) ? $item->segmento : []))) selected @endif value="produtos">Produtos</option>
            <option @if(in_array('servicos', (isset($item) ? $item->segmento : []))) selected @endif value="servicos">Serviços</option>
        </select>
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo_divisao_pizza', 'Tipo divisão para pizza', [
        'divide' => 'Divide', 'valor_maior' => 'Valor da maior'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('status', 'Status', [1 => 'Ativo', 0 => 'Desativado'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::select('notificacao_novo_pedido', 'Notificação de novo pedido', [1 => 'Sim', 0 => 'Não'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::select('autenticacao_sms', 'Autenticação SMS para novo cadastro', [1 => 'Sim', 0 => 'Não'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('confirmacao_pedido_cliente', 'Confirmar pedido do cliente', [1 => 'Sim', 0 => 'Não'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-lg-6 col-12">
        <label for="">Tipos de pagamento</label>
        <select class="select2 form-control select2-multiple" name="tipos_pagamento[]" data-toggle="select2" multiple="multiple" id="tipos_pagamento">
            @foreach(\App\Models\MarketPlaceConfig::tiposPagamento() as $t)
            <option @if(in_array($t, (isset($item) ? $item->tipos_pagamento : []))) selected @endif value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        {!!Form::text('funcionamento_descricao', 'Descreva o funcionamento')
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('mercadopago_public_key', 'Mercado Pago Public Key')
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('mercadopago_access_token', 'Mercado Pago Access Token')
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('token_whatsapp', 'Token WhatsApp')
        !!}
        <h5 class="text-success mt-1">Para enviar mensagens de alertas de agendamento https://criarwhats.com</h5>
    </div>
    <hr>
    <div class="col-md-3">
        <label>Token</label>
        <button type="button" class="btn btn-link btn-tooltip btn-sm" data-toggle="tooltip" data-placement="top" title="Esse Token é inserido no app antes do build, para conectar o App com este servidor"><i class="ri-file-info-fill"></i></button>
        <div class="input-group">
            <input readonly type="text" class="form-control tooltipp" id="api_token" name="api_token" value="{{ isset($item) ? $item->api_token : '' }}">
            <button type="button" class="btn btn-info" id="btn_token"><a class="ri-refresh-line text-white"></a></button>
        </div>
        @if($errors->has('api_token'))
        <label class="text-danger">Campo Obrigatório</label>
        @endif
    </div>

    <div class="col-md-2">
        {!!Form::text('loja_id', 'ID loja')
        ->attrs(['class' => 'tooltipp'])
        !!}
        <div class="text-tooltip d-none">
            Para utilizar o delivery modelo link
        </div>
    </div>

    <div class="col-md-2">
        {!!Form::text('cor_principal', 'Cor principal')
        ->attrs(['class' => 'tooltipp'])
        ->type('color')
        !!}
    </div>
    
    <hr>
    <div class="card col-md-3 mt-3 form-input" style="width: 210px">
        <div class="preview">
            <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
            @isset($item)
            <img id="file-ip-1-preview" src="{{ $item->logo_img }}">
            @else
            <img id="file-ip-1-preview" src="/imgs/no-image.png">
            @endif
        </div>
        <label for="file-ip-1">Logo</label>
        <input type="file" id="file-ip-1" name="logo_image" accept="image/*" onchange="showPreview(event);">
    </div>

    <!-- <div class="card col-md-3 mt-3 form-input" style="margin-left: 5px">
        <div class="preview">
            <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
            @isset($item)
            <img id="file-ip-2-preview" src="{{ $item->fav_img }}">
            @else
            <img id="file-ip-2-preview" src="/imgs/no-image.png">
            @endif
        </div>
        <label for="file-ip-2">FavIcon</label>
        <input type="file" id="file-ip-2" name="fav_icon_image" accept="image/*" onchange="showPreview2(event);">
    </div> -->

    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script type="text/javascript">
    $('#btn_token').click(() => {

        let token = generate_token(25);
        swal({
            title: "Atenção", 
            text: "Esse token é o responsavel pela comunicação com a API, tenha atenção!!",
            icon: "warning",
            buttons: true,
            dangerMode: true
        }).then((confirmed) => {
            if (confirmed) {
                $('#api_token').val(token)
            }
        });
    })

    function generate_token(length) {
        var a = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890".split("");
        var b = [];
        for (var i = 0; i < length; i++) {
            var j = (Math.random() * (a.length - 1)).toFixed(0);
            b[i] = a[j];
        }
        return b.join("");
    }

</script>
@endsection
