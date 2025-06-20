@extends('layouts.app', ['title' => 'Notificação'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>{{ $item->titulo }}</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('home') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!! $item->descricao !!}
        <hr>
        <p class="float-end">{{ __data_pt($item->created_at) }}</p>
    </div>
</div>

@endsection
