@extends('layouts.app', ['title' => 'Nova Interrupção'])

@section('content')

<div class="mt-3">
    <div class="row">
        {!!Form::open()
        ->post()
        ->route('interrupcoes.store')
        !!}
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h4>Nova Interrupção</h4>
                    <hr>
                    @include('interrupcoes._forms')
                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
</div>

@endsection
