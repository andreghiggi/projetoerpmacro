@extends('layouts.app', ['title' => 'Configuração IFood'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração IFood</h4>
        
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('ifood-config.store')
        !!}
        <div class="pl-lg-4">
            @include('ifood._forms_config')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
