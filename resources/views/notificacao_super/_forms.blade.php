<div class="row g-2">

    <div class="col-md-3">
        {!!Form::text('titulo', 'Título')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Status', ['1' => 'Ativo', '0' => 'Desativado'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('prioridade', 'Prioridade', ['' => 'Selecione', 'baixa' => 'Baixa', 'media' => 'Média', 'alta' => 'Alta'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('descricao_curta', 'Descrição curta')->required()
        !!}
    </div>

    @if(!isset($item))
    <div class="col-md-4">
        {!!Form::select('empresa', 'Empresa')
        ->options((isset($item) && $item->empresa) ? [$item->empresa_id => $item->empresa->info] : [])
        !!}
    </div>
    @else
    <div class="col-md-4">
        {!!Form::text('emp', 'Empresa')
        ->value($item->empresa ? $item->empresa->nome : '')
        ->readonly(true)
        !!}
    </div>
    @endif

    <div class="col-md-12">
        {!!Form::textarea('descricao', 'Descrição')
        ->attrs(['rows' => '7', 'class' => 'tiny'])
        !!}
    </div>

    <hr class="mt-4">
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
    })

</script>
@endsection