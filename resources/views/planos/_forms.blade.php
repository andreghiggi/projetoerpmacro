<div class="row g-2">
    <div class="col-md-3">
        {!!Form::text('nome', 'Nome')
        ->required()
        !!}
    </div>

    <div class="col-md-5">
        {!!Form::text('descricao_curta', 'Descrição curta (landing page)')
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('maximo_nfes', 'Max. NFe (mês)')
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('maximo_nfces', 'Max. NFCe (mês)')
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('maximo_ctes', 'Max. CTe (mês)')
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('maximo_mdfes', 'Max. MDFe (mês)')
        ->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('maximo_usuarios', 'Max. Usuários')
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('maximo_locais', 'Max. Locais')
        ->required()
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    
    <div class="col-md-2">
        {!!Form::select('visivel_clientes', 'Visível para clientes', ['1' => 'Sim', '0' => 'Não'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('visivel_contadores', 'Visível para contadores', ['0' => 'Não', '1' => 'Sim'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('dias_teste', 'Dias de teste')
        ->attrs(['class' => 'tooltipp'])
        !!}
        <div class="text-tooltip d-none">
            Dias para testar, somente para auto cadastro
        </div>
    </div>
    <div class="col-md-2">
        {!!Form::tel('intervalo_dias', 'Intervalo de dias')
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::tel('valor', 'Valor')
        ->required()
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->valor) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('valor_implantacao', 'Valor de implantação')
        ->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->valor_implantacao) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('auto_cadastro', 'Auto cadastro cliente', ['0' => 'Não', '1' => 'Sim'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('fiscal', 'Emite fiscal', ['1' => 'Sim', '0' => 'Não'])
        ->required()
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('segmento_id', 'Segmento', ['' => 'Selecione'] + $segmentos->pluck('nome', 'id')->all())
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <p class="text-danger">A função "Auto cadastro cliente" se marcada este plano será incluído ao cliente quando o mesmo se cadastrar!</p>

    <div class="col-12"></div>

    <div class="col-md-12">
        {!!Form::textarea('descricao', 'Descrição')
        ->attrs(['rows' => '10', 'class' => 'tiny'])
        !!}
    </div>

    <div class="row m-3">
        <h5>Módulos do plano</h5>
        @if(!isset($item))
        <div class="row">
            <div class="col-lg-3 col-6">
                <input type="checkbox" class="form-check-input check_todos" style=" width: 25px; height: 25px;">
                <label class="form-check-label m-1" for="customCheck1">Marcar todos</label>
            </div>
        </div>
        @endif
        @foreach($modulos as $m)
        <div class="col-lg-3 col-6">
            <input name="modulos[]" value="{{$m}}" type="checkbox" class="form-check-input check-module" style=" width: 25px; height: 25px;" @isset($item) @if(sizeof($item->modulos) > 0 && in_array($m, $item->modulos)) checked="true" @endif @endif>
            <label class="form-check-label m-1" for="customCheck1">{{$m}}</label>
        </div>
        @endforeach
    </div>

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
    <div class="text-danger mt-2">
        {{ $errors->first('image') }}
    </div>
    @endif
    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script src="/tinymce/tinymce.min.js"></script>
<script type="text/javascript">

    $(function(){
        @if(!isset($item))
        setTimeout(() => {
            checkTodos()
        }, 10)
        @endif

        tinymce.init({ selector: 'textarea.tiny', language: 'pt_BR'})

        setTimeout(() => {
            $('.tox-promotion, .tox-statusbar__right-container').addClass('d-none')
        }, 500)
    })

    $('body').on('click', '.check_todos', function () {
        setTimeout(() => {
            checkTodos()
        }, 10)
    })

    function checkTodos(){

        if($('.check_todos').is(':checked')){
            $('.check-module').prop('checked', 1)
        }else{
            $('.check-module').prop('checked', 0)
        }
    }
</script>
@endsection
