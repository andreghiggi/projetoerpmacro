@extends('layouts.app', ['title' => 'Configuração de MarketPlace'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração de MarketPlace</h4>

    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('config-marketplace.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('marketplace_config._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
