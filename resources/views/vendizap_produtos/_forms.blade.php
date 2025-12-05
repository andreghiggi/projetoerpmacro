<div class="row g-2">
    <div class="col-md-2">
        {!!Form::text('nome', 'Nome')
        ->value($data->descricao)
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('preco', 'Preço')
        ->value($data->preco ? __moeda($data->preco) : '')
        ->attrs(['class' => 'moeda'])
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('promocao', 'Promoção')
        ->value($item->promocao ? __moeda($item->promocao) : '')
        ->attrs(['class' => 'moeda'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('exibir', 'Exibir', [1 => 'Sim', 0 => 'Não'])
        ->attrs(['class' => 'form-select'])
        ->value($data->exibir)
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('destaque', 'Destaque', [1 => 'Sim', 0 => 'Não'])
        ->attrs(['class' => 'form-select'])
        ->value($data->destaque)
        !!}
    </div>

    @if(sizeof($data->variacoes) == 0)
    <div class="col-md-2">
        {!!Form::tel('estoque', 'Estoque')
        ->attrs(['class' => 'quantidade'])
        ->value($item->estoque)
        !!}
    </div>
    @endif



    <div class="col-md-3">

        <label for="">Categorias</label>
        <select class="select2 form-control select2-multiple" name="categorias[]" data-toggle="select2" multiple="multiple" id="categorias">
            @foreach($categorias as $c)
            <option @if(in_array($c->_id, $categoriaSelect)) selected @endif value="{{ $c->_id }}">{{ $c->nome }}</option>
            @endforeach
        </select>
    </div>


    <div class="col-md-3">
        {!!Form::text('codigo', 'Código')
        ->value($data->codigo)
        !!}
    </div>

    <div class="col-md-6">
        {!!Form::text('video', 'Vídeo')
        ->value($data->video)
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('largura', 'Largura (cm)')
        ->value($data->dimensoes->largura)
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('comprimento', 'Comprimento (cm)')
        ->value($data->dimensoes->comprimento)
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('altura', 'Altura (cm)')
        ->value($data->dimensoes->altura)
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('peso', 'Peso')
        ->value($data->dimensoes->peso)
        !!}
    </div>

    @if(sizeof($data->variacoes) > 0)
    <hr>
    <div class="col-md-8">

        <h5>Variações</h5>
        <div class="row">

            <table class="table table-dynamic">
                <thead class="table-success">
                    <tr>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Estoque</th>
                        <th>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($variacoes as $v)
                    <tr>
                        <td>
                            <input type="hidden" class="form-control" name="variacao[]" value="{{ $v['variacao'] }}">
                            <input type="hidden" class="form-control" name="variacao_id[]" value="{{ $v['variacao_id'] }}">
                            <input type="hidden" class="form-control" name="variavel_id[]" value="{{ $v['variavel_id'] }}">

                            @if(isset($v['variacao_id2']))
                            <input type="hidden" class="form-control" name="variacao_id2[]" value="{{ $v['variacao_id2'] }}">
                            @endif

                            @if(isset($v['variavel_id2']))
                            <input type="hidden" class="form-control" name="variavel_id2[]" value="{{ $v['variavel_id2'] }}">
                            @endif
                            
                            <input type="text" class="form-control" name="descricao_variacao[]" value="{{ $v['descricao'] }}" required readonly>
                        </td>
                        <td>
                            <input type="tel" class="form-control moeda" name="valor_variacao[]" value="{{ __moeda($v['valor']) }}" required>
                        </td>

                        <td>
                            <input type="text" class="form-control ignore qtd" name="estoque_variacao[]" value="{{ $v['quantidade'] }}">
                        </td>
                        
                        <td>
                            <button type="button" class="btn btn-sm btn-danger btn-remove-tr-variacao">
                                <i class="ri-subtract-line"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif


    <div class="col-md-6">
        {!!Form::textarea('detalhes', 'Detalhes')
        ->attrs(['rows' => '2', 'class' => 'tiny'])
        ->value($data->detalhes)
        !!}
    </div>

    <hr>
    <div class="card col-md-3 mt-3 form-input">
        <div class="preview">
            <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
            @if(isset($item) && $item->imagemUrl != null)
            <img id="file-ip-1-preview" src="{{ $item->imagemUrl }}">
            @else
            <img id="file-ip-1-preview" src="/imgs/no-image.png">
            @endif
        </div>
        <label for="file-ip-1">Imagem</label>

        <input type="file" id="file-ip-1" name="image" accept="image/*" onchange="showPreview(event);">
    </div>


    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script src="/tinymce/tinymce.min.js"></script>
<script type="text/javascript">

    $('#btn-store').click(() => {
        $body = $("body");
        $body.addClass("loading");
    })

    $(function(){
        tinymce.init({ selector: 'textarea.tiny', language: 'pt_BR'})

        setTimeout(() => {
            $('.tox-promotion, .tox-statusbar__right-container').addClass('d-none')
        }, 500)

    })

    $(document).delegate(".btn-remove-tr-variacao", "click", function (e) {
        e.preventDefault();
        swal({
            title: "Você esta certo?",
            text: "Deseja remover esse item mesmo?",
            icon: "warning",
            buttons: true
        }).then(willDelete => {
            if (willDelete) {
                var trLength = $(this)
                .closest("tr")
                .closest("tbody")
                .find("tr")
                .not(".dynamic-form-document").length;
                if (!trLength || trLength > 1) {
                    $(this)
                    .closest("tr")
                    .remove();
                } else {
                    swal("Atenção", "Você deve ter ao menos um item na lista", "warning");
                }
            }
        });
    });


</script>
@endsection