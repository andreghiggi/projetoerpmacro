@section('css')
<style type="text/css">
    input[type="file"] {
        display: none;
    }

    .file label {
        padding: 8px 8px;
        width: 100%;
        background-color: #8833FF;
        color: #FFF;
        text-transform: uppercase;
        text-align: center;
        display: block;
        margin-top: 20px;
        cursor: pointer;
        border-radius: 5px;
    }

    .card-body strong{
        color: #8833FF;
    }

</style>
@endsection

@if(__countLocalAtivo() > 1 && __escolheLocalidade())
<div class="row mb-2">
    <div class="col-md-3">
        <label for="">Local</label>
        <select id="inp-local_id" required class="select2 class-required" data-toggle="select2" name="local_id">
            <option value="">Selecione</option>
            @foreach(__getLocaisAtivoUsuario() as $local)
            <option @isset($item) @if($item->local_id == $local->id) selected @endif @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
            @endforeach
        </select>
    </div>
</div>
@endif


<div class="row g-2">

    <div class="col-md-4">
        <label class="required">Cliente</label>
        <div class="input-group flex-nowrap">
            <select required id="inp-cliente_id" name="cliente_id" class="cliente_id">
                @if(isset($item) && $item->cliente)
                <option value="{{ $item->cliente_id }}">{{ $item->cliente->razao_social }}</option>
                @endif
            </select>
            @can('clientes_create')
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modal_novo_cliente" type="button">
                <i class="ri-add-circle-fill"></i>
            </button>
            @endcan
        </div>
    </div>

    <div class="col-md-2">
        {!!Form::select('estado', 'Estado', App\Models\ProjetoCusto::estados())
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::date('data_prevista_entrega', 'Data Prevista Entrega')
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('observacao', 'Observação')
        !!}
    </div>

    <div class="col-md-12">
        {!!Form::textarea('descricao', 'Descrição')
        ->attrs(['rows' => '6'])
        !!}
    </div>

    <div class="col-md-3 file">
        {!! Form::file('file', 'Arquivo')
        ->attrs(['accept' => '']) !!}
        <span class="text-danger" id="filename"></span>
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success btn-salvar px-5 m-3">Salvar</button>
    </div>
</div>