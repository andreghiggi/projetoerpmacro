@extends('layouts.app', ['title' => 'Editar Interrupção'])

@section('content')

<div class="mt-3">
    <div class="row">
        {!!Form::open()->fill($item)
        ->put()
        ->route('interrupcoes.update', [$item->id])
        !!}
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h4>Editar Interrupção</h4>
                    <hr>
                    @include('interrupcoes._forms')
                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
</div>

@endsection
