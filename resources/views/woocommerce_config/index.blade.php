@extends('layouts.app', ['title' => 'Configuração Woocommerce'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração Woocommerce</h4>

    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('woocommerce-config.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('woocommerce_config._forms')
        </div>
        {!!Form::close()!!}


    </div>
</div>
@endsection
