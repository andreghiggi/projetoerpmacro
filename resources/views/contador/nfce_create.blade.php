@extends('layouts.app', ['title' => 'Nova NFCe'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Nova NFCe</h4>
        <div style="text-align: right; margin-top: -35px;">

            <a href="{{ route('contador-empresa.nfce') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->id('form-nfce')
        ->route('contador-empresa-nfce.store')
        !!}
        <div class="pl-lg-4">
            @include('nfce._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@include('modals._novo_cliente')

@section('js')
<script type="text/javascript">
    $(function(){
        $('#empresa_id').val('{{ $empresaSelecionada }}')
        $('#usuario_id').val('{{ $user_id }}')
    })
</script>
<script src="/js/nfce.js"></script>
<script src="/js/novo_cliente.js"></script>

@endsection
@endsection
