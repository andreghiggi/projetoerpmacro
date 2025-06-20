@extends('front_box.default', 
['title' => !isset($title) ? (isset($pedido) ? isset($isDelivery) ? ('Finalizando Pedido Delivery ' . $pedido->id) : ('Finalizando Comanda ' . $pedido->comanda) : 'Nova Venda - PDV') : $title ])
@section('content')

{!!Form::open()
->post()
->route('frontbox.store')->id('form-pdv')
!!}
<div class="pl-lg-4">
    @include('front_box._forms')
</div>
{!!Form::close()!!}

@include('modals._novo_cliente')

@endsection




