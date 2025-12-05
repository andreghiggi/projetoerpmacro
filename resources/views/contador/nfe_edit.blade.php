@extends('layouts.app', ['title' => $item->tpNF == 0 ? 'Editar Compra' : (isset($isOrcamento) && $isOrcamento == 1 ? 'Editar orçamento' : 'Editar Venda')])
@section('content')

<div class="card mt-1">
    <div class="card-header">

        <h4>{{ $item->orcamento ? 'Editar Orçamento' : ($item->tpNF == 1 ? 'Editar Venda' : 'Editar Compra') }}</h4>
        <div style="text-align: right; margin-top: -35px;">
            @if(__countLocalAtivo() > 1 && isset($caixa))
            <h5 class="mt-2">Local: <strong class="text-danger">{{ $caixa->localizacao ? $caixa->localizacao->descricao : '' }}</strong></h5>
            @endif
            <a href="{{ route('contador-empresa.nfe') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('contador-empresa-nfe.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('nfe._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>

@include('modals._dimensao_item_nfe')

@section('js')
<script type="text/javascript">
    $(function(){
        $('#empresa_id').val('{{ $empresaSelecionada }}')
        $('#usuario_id').val('{{ $item->user_id }}')

    })
</script>
<script src="/js/nfe.js"></script>
@endsection
@endsection
