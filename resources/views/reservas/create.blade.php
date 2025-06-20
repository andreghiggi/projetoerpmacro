@extends('layouts.app', ['title' => 'Nova Reserva'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova Reserva</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('reservas.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('reservas.store')

        !!}
        <div class="pl-lg-4">
            @include('reservas._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@include('modals._novo_cliente')

@endsection
@section('js')
<script type="text/javascript" src="/js/reserva.js"></script>
<script src="/js/novo_cliente.js"></script>

@endsection


