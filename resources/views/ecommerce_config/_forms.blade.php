<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('loja_id', 'ID Loja')
        ->required()
        !!}
    </div>

    <div class="col-md-7">
        {!!Form::text('descricao_breve', 'Descrição curta')
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
        {!!Form::tel('telefone', 'Telefone')
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

  <!--   <div class="col-md-2">
        {!!Form::text('desconto_padrao_boleto', '%Desconto para boleto')
        ->attrs(['class' => 'percentual'])
        ->value(isset($item) ? $item->desconto_padrao_boleto : '')
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('desconto_padrao_pix', '%Desconto para pix')
        ->attrs(['class' => 'percentual'])
        ->value(isset($item) ? $item->desconto_padrao_pix : '')
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('desconto_padrao_cartao', '%Desconto para cartão')
        ->attrs(['class' => 'percentual'])
        ->value(isset($item) ? $item->desconto_padrao_cartao : '')
        !!}
    </div> -->

    <div class="col-md-2">
        {!!Form::text('frete_gratis_valor', 'Valor para frete gratis')
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->frete_gratis_valor) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Status da loja', [1 => 'Ativo', 0 => 'Desativado'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('habilitar_retirada', 'Habilitar retirada', [1 => 'Sim', 0 => 'Não'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('notificacao_novo_pedido', 'Notificação de novo pedido', [1 => 'Sim', 0 => 'Não'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>


    <div class="col-lg-3 col-12">
        <label for="">Tipos de pagamento</label>
        <select required class="select2 form-control select2-multiple" name="tipos_pagamento[]" data-toggle="select2" multiple="multiple" id="tipos_pagamento">
            @foreach(\App\Models\EcommerceConfig::tiposPagamento() as $t)
            <option @if($item != null) @if(in_array($t, $item->tipos_pagamento)) selected @endif @endif value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        {!!Form::text('mercadopago_public_key', 'Mercado Pago Public Key')->required()
        !!}
    </div>

    <div class="col-md-5">
        {!!Form::text('mercadopago_access_token', 'Mercado Pago Access Token')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('cor_principal', 'Cor principal')
        ->attrs(['class' => 'tooltipp'])
        ->type('color')
        !!}
    </div>

    <div class="col-12"></div>

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

    <div class="col-md-12">
        {!!Form::textarea('politica_privacidade', 'Politica de privacidade')
        ->attrs(['rows' => '10', 'class' => 'tiny'])
        !!}
    </div>
    <hr>
    <div class="col-md-12">
        {!!Form::textarea('termos_condicoes', 'Termos e condições')
        ->attrs(['rows' => '10', 'class' => 'tiny'])
        !!}
    </div>

    <div class="col-md-12 d-none d-deposito">
        {!!Form::textarea('dados_deposito', 'Dados para depósito bancário')
        ->attrs(['rows' => '10', 'class' => 'tiny'])
        !!}
    </div>

    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script src="/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    $(function(){
        tinymce.init({ selector: 'textarea.tiny', language: 'pt_BR'})

        setTimeout(() => {
            $('.tox-promotion, .tox-statusbar__right-container').addClass('d-none')
        }, 500)
        changeTipo()
    })

    $('#tipos_pagamento').change(() => {
        changeTipo()
    })

    function changeTipo(){
        let tipos = $('#tipos_pagamento').val()

        if(tipos.includes("Depósito bancário")){
            $('.d-deposito').removeClass('d-none')
        }else{
            $('.d-deposito').addClass('d-none')
        }
    }
</script>
@endsection
