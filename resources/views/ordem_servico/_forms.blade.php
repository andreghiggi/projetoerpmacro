<div class="row g-2">
    @if(__countLocalAtivo() > 1)
    <div class="col-md-2">
        <label for="">Local</label>

        <select id="inp-local_id" required class="select2 class-required" data-toggle="select2" name="local_id">
            <option value="">Selecione</option>
            @foreach(__getLocaisAtivoUsuario() as $local)
            <option @isset($item) @if($item->local_id == $local->id) selected @endif @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
            @endforeach
        </select>
    </div>
    @else
    <input id="inp-local_id" type="hidden" value="{{ __getLocalAtivo() ? __getLocalAtivo()->id : '' }}" name="local_id">
    @endif
    
    <div class="col-md-4">
        {!!Form::select('cliente_id', 'Cliente')->attrs(['class' => 'select2'])->options(isset($item) ? [$item->cliente_id => $item->cliente->razao_social] : [])
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        <label class="">Início</label>
        <input required type="text" name="data_inicio" id="datetime-datepicker" class="form-control" value="{{ isset($item) ? $item->data_inicio : '' }}">
        @if($errors->has('data_inicio'))
        <label class="text-danger">Campo Obrigatório</label>
        @endif
    </div>
    <!-- <div class="col-md-2">
        <label class="">Previsão de Entrega</label>
        <input type="text" name="data_entrega" id="datetime-datepicker2" class="form-control" value="{{ isset($item) ? $item->data_entrega : '' }}">
        @if($errors->has('data_entrega'))
        <label class="text-danger">Campo Obrigatório</label>
        @endif
    </div> -->
    <div class="col-md-3">
        {!!Form::select('funcionario_id', 'Funcionário', ['' => 'Selecione'] + $funcionario->pluck('nome', 'id')->all())->attrs(['class' => 'form-select'])->required(__isSegmentoPlanoOtica())
        !!}
    </div>

    <div class="col-md-12">
        {!!Form::textarea('descricao', 'Descrição/Observação')
        ->attrs(['rows' => '6', 'class' => 'tiny'])
        !!}
    </div>

    @if(__isSegmentoPlanoOtica())
    @include('ordem_servico.partials.otica_forms')
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
        tinymce.init({ selector: 'textarea.tiny', language: 'pt_BR'})

        setTimeout(() => {
            $('.tox-promotion, .tox-statusbar__right-container').addClass('d-none')
        }, 500)
    })
</script>
@endsection
