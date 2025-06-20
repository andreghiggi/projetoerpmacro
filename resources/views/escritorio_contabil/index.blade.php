@extends('layouts.app', ['title' => 'Escritório Contábil'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Escritório Contábil</h4>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('escritorio-contabil.store')
        !!}
        <div class="pl-lg-4">
            @include('escritorio_contabil._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
