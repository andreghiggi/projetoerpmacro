@extends('layouts.app', ['title' => 'Arquivo Sped ICMS'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Arquivo Sped ICMS</h4>

    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('sped.store')
        !!}
        <div class="pl-lg-4">
            @include('sped._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
