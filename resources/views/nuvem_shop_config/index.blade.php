@extends('layouts.app', ['title' => 'Configuração Nuvem Shop'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração Nuvem Shop</h4>

    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('nuvem-shop-config.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('nuvem_shop_config._forms')
        </div>
        {!!Form::close()!!}


    </div>
</div>
@endsection
