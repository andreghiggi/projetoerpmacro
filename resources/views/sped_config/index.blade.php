@extends('layouts.app', ['title' => 'Configuração de Sped'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração de Sped</h4>

    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('sped-config.store')
        !!}
        <div class="pl-lg-4">
            @include('sped_config._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
