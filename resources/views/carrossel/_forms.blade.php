<div class="row g-2">
    <div class="col-md-6">
        {!!Form::text('descricao', 'Descrição')
        !!}
    </div>
    @if(__isInternacionalizar(Auth::user()->empresa))
    <div class="col-md-6">
        {!!Form::text('descricao_en', 'Descrição (em inglês)')
        !!}
    </div>
    <div class="col-md-6">
        {!!Form::text('descricao_es', 'Descrição (em espanhol)')
        !!}
    </div>
    @endif

    <div class="col-md-4">
        {!!Form::select('produto_id', 'Produto', ['' => 'Selecione'] + $produtos->pluck('nome', 'id')->all())
        ->attrs(['class' => 'select2'])
        ->id('prod')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('valor', 'Valor')
        ->value(isset($item) ? __moeda($item->valor) : '')
        ->attrs(['class' => 'moeda'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-12"></div>

    <div class="card col-md-3 mt-3 form-input">
        <div class="preview">
            <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
            @isset($item)
            <img id="file-ip-1-preview" src="{{ $item->img }}">
            @else
            <img id="file-ip-1-preview" src="/imgs/no-image.png">
            @endif
        </div>
        <label for="file-ip-1">Imagem</label>
        <input type="file" id="file-ip-1" name="image" accept="image/*" onchange="showPreview(event);">


    </div>
    @if($errors->has('image'))
    <div class="text-danger">
        {{ $errors->first('image') }}
    </div>
    @endif
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script type="text/javascript">
    $("#inp-produto_id").change(() => {
        let product_id = $("#inp-produto_id").val();
        if (product_id) {
            $.get(path_url + "api/produtos/findId/" + product_id)
            .done((e) => {
                if(e.valor_cardapio == null){
                    valor = e.valor_unitario
                }else{
                    valor = e.valor_cardapio
                }
                $("#inp-valor").val(convertFloatToMoeda(valor));
            })
            .fail((e) => {
                console.log(e);
            });
        }
    });

</script>
@endsection