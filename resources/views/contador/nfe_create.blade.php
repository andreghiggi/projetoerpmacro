@extends('layouts.app', ['title' => 'Nova Venda'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
       
        <h4>Nova Venda - NFe</h4>

        <input type="hidden" id="is_orcamento" value="0">

        <div style="text-align: right; margin-top: -35px;">
            
            <a href="{{ !isset($isCompra) ? route('nfe.index') : route('contador-empresa.nfe') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>

        </div>
    </div>
    <div class="card-body">

        {!!Form::open()
        ->post()
        ->id('form-nfe')
        ->route('contador-empresa-nfe.store')
        !!}
        <div class="pl-lg-4">
            @include('nfe._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@isset($isCompra)
@include('modals._novo_fornecedor')
@else
@include('modals._novo_cliente')
@endif

@include('modals._dimensao_item_nfe')

@section('js')

<script type="text/javascript">

	$(function(){
		$('#empresa_id').val('{{ $empresaSelecionada }}')
        $('#usuario_id').val('{{ $user_id }}')
	})

    $(".tipo_pagamento").change(() => {
        let tipo = $(".tipo_pagamento").val();
        if (tipo == "03" || tipo == "04") {
            $('#cartao_credito').modal('show')
        }
    })
</script>

<script src="/js/nfe.js"></script>
@isset($isCompra)
<script src="/js/novo_fornecedor.js"></script>
@else
<script src="/js/novo_cliente.js"></script>
@endif
@endsection
@endsection
