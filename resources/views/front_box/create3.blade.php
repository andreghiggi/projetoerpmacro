@extends('front_box.default', 
['title' => !isset($title) ? (isset($pedido) ? isset($isDelivery) ? ('Finalizando Pedido Delivery ' . $pedido->id) : ('Finalizando Comanda ' . $pedido->comanda) : 'Nova Venda - PDV') : $title ])
@section('content')
@section('css')
<link href="/css/pdv3.css" rel="stylesheet"/>
@endsection

{!!Form::open()
->post()
->route('frontbox.store')->id('form-pdv')
!!}
<div class="pl-lg-4">
    @include('front_box._forms3')

</div>
{!!Form::close()!!}

@include('front_box.partials_form3.modal_desconto')
@include('front_box.partials_form3.modal_acrescimo')
@include('front_box.partials_form3.modal_frete')
@include('front_box.partials_form3.modal_observacao')
@include('front_box.partials_form3.modal_cartao')
@include('front_box.partials_form3.modal_finalizar')
@include('modals._cpf_nota', ['not_submit' => true])
@include('front_box.partials_form3.modal_cliente')
@include('front_box.partials_form3.modal_vendedor')
@include('front_box.partials_form3.modal_fatura')
@include('front_box.partials_form3.modal_dinheiro')
@include('front_box.partials_form3.modal_acoes')
@include('front_box.partials_form3.modal_vendas_off')
@include('front_box.partials_form3.modal_suprimento')
@include('front_box.partials_form3.modal_sangria')
@include('front_box.partials_form3.modal_lista_preco')
@include('front_box.partials_form3.edit_item')
@include('modals._vendas_suspensas')

@section('js')
<script type="text/javascript" src="/js/frente_caixa3.js"></script>
@endsection
@endsection




