@extends('layouts.app', ['title' => 'Configuração Conecta Venda'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração Conecta Venda</h4>

    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('conecta-venda-config.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('conecta_venda_config._forms')
        </div>
        {!!Form::close()!!}


    </div>
</div>
@endsection
