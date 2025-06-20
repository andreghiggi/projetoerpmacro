@extends('layouts.app', ['title' => 'Configuração Cardápio'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração Cardápio</h4>

    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('config-cardapio.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('cardapio.config._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
