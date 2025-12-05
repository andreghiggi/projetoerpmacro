@extends('layouts.app', ['title' => 'Nova Mesa'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Mesa</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('mesas.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('mesas.store')
        !!}
        <div class="pl-lg-4">
            @include('mesas._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

