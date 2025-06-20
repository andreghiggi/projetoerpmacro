@extends('layouts.app', ['title' => 'Configuração Ecommerce'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração Ecommerce</h4>

    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('config-ecommerce.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('ecommerce_config._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
