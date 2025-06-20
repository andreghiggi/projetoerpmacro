@extends('layouts.app', ['title' => 'Checkin #' . $item->numero_sequencial])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Checkin <strong>#{{ $item->numero_sequencial }}</strong></h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('reservas.show', [$item->id]) }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->id('form-checkin')
        ->route('reservas.checkin-start', [$item->id])
        !!}
        <div class="pl-lg-4">

            @include('reservas.partials._form_checkin')

        </div>
        {!!Form::close()!!}
    </div>
</div>
@include('modals._novo_cliente')

@endsection

@section('js')
<script src="/assets/vendor/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<script src="/assets/js/pages/demo.form-wizard.js"></script>
<script type="text/javascript" src="/js/reserva.js"></script>
@endsection


