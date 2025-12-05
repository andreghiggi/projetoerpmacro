<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome_restaurante', 'Nome restaurante')
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('descricao_restaurante_pt', 'Descrição restaurante')
        ->required()
        !!}
    </div>

    @if(__isInternacionalizar(Auth::user()->empresa))
    <div class="col-md-4">
        {!!Form::text('descricao_restaurante_en', 'Descrição restaurante (em inglês)')
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('descricao_restaurante_es', 'Descrição restaurante (em espanhol)')
        !!}
    </div>
    @endif

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
        {!!Form::tel('telefone', 'Telefone')
        ->attrs(['class' => 'fone'])
        ->required()
        !!}
    </div>

    <div class="col-md-4">
        <label class="required">Token</label>
        <button type="button" class="btn btn-link btn-tooltip btn-sm" data-toggle="tooltip" data-placement="top" title="Esse Token é inserido para conectar o App com este servidor"><i class="ri-file-info-fill"></i></button>
        <div class="input-group">
            <input readonly type="text" class="form-control tooltipp" id="api_token" name="api_token" value="{{ isset($item) ? $item->api_token : '' }}">
            <button type="button" class="btn btn-info" id="btn_token"><a class="ri-refresh-line text-white"></a></button>
        </div>
        @if($errors->has('api_token'))
        <label class="text-danger">Campo Obrigatório</label>
        @endif
    </div>

    <div class="col-md-4">
        {!!Form::text('link_instagran', 'Instagram')
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::text('link_facebook', 'Facebook')
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::text('link_whatsapp', 'WhatsApp')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('intercionalizar', 'Internacionalizar', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('valor_pizza', 'Valor para pizza', [
        'divide' => 'Divide', 'valor_maior' => 'Valor da maior'
        ])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('incluir_servico', 'Incluir serviço', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('percentual_taxa_servico', '% taxa de serviço')
        ->attrs(['class' => 'percentual'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('qr_code_mesa', 'Qr Code mesa', [0 => 'Não', 1 => 'Sim'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('limite_pessoas_qr_code', 'Limite de pessoas por QR Code')
        ->attrs(['data-mask' => '00'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('cor_principal', 'Cor principal')
        ->attrs(['class' => 'tooltipp'])
        ->type('color')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('confirma_mesa', 'Confirmar mesa automatico', [1 => 'Sim', 0 => 'Não'])
        ->attrs(['class' => 'form-select'])
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
            title: "Atenção"
            , text: "Esse token é o responsavel pela comunicação com a API, tenha atenção!!"
            , icon: "warning"
            , buttons: true
            , dangerMode: true
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
