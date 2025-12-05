@extends('layouts.app', ['title' => 'Configuração VendiZap'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração VendiZap</h4>

    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('vendizap-config.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('vendizap_config._forms')
        </div>
        {!!Form::close()!!}


    </div>
</div>
@endsection
