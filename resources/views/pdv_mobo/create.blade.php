@extends('pdv_mobo.default', ['title' => $title ])
@section('content')

<!-- {!!Form::open()
->post()
->route('frontbox.store')->id('form-pdv')
!!} -->
<div class="pl-lg-4">
    @include('pdv_mobo._forms')
</div>
<!-- {!!Form::close()!!} -->


@include('pdv_mobo.partials.modal_produto')
@include('pdv_mobo.partials.modal_carrinho')
@include('pdv_mobo.partials.modal_cliente')
@include('pdv_mobo.partials.modal_finalizar')
@include('pdv_mobo.partials.modal_sangria')
@include('pdv_mobo.partials.modal_suprimento')
@include('pdv_mobo.partials.modal_vendas_suspensa')
@include('pdv_mobo.partials.modal_adicionais')
@include('pdv_mobo.partials.modal_comandas')
@include('pdv_mobo.partials.modal_vendas_diaria')
@include('pdv_mobo.partials.modal_frete')
@endsection
@section('js')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    var clientes = @json($clientes);
</script>

<script type="text/javascript" src="/js/pdv_mobo.js"></script>
<script type="text/javascript">
    @if($isComanda == 1)
    carrinho = @json($itens);
    atualizarBotaoCarrinho();

    @if($item->cliente)
    clienteSelecinado = '{{ $item->cliente->id }}'
    $('#clienteNome').text('{{ $item->cliente->razao_social }}');
    @endif
    @endif

    // isVendaSuspensa
    @if($isVendaSuspensa == 1)
    carrinho = @json($itens);
    atualizarBotaoCarrinho();

    @if($item->cliente)
    clienteSelecinado = '{{ $item->cliente->id }}'
    $('#clienteNome').text('{{ $item->cliente->razao_social }}');
    @endif
    @endif
</script>

@endsection
