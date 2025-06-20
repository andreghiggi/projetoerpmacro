@extends('layouts.app', ['title' => 'Nova NFSe'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        @isset($reserva)
        <p>Serviços da reserva <strong>#{{ $reserva->numero_sequencial }}</strong></p>
        @endif
        <h4>Nova NFSe</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('nota-servico.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('nota-servico.store')

        !!}
        @isset($reserva)
        <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">
        @endif

        <div class="pl-lg-4">
            @include('nota_servico._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')
<script src="/js/nfse.js"></script>
@isset($reserva)
<script type="text/javascript">
    $(function(){
        setTimeout(() => {
            $('.cliente_id').change()
        }, 200)
    })
</script>
@endif
@endsection
