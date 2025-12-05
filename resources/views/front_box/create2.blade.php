@extends('front_box.default', 
['title' => !isset($title) ? (isset($pedido) ? isset($isDelivery) ? ('Finalizando Pedido Delivery ' . $pedido->id) : ('Finalizando Comanda ' . $pedido->comanda) : 'Nova Venda - PDV') : $title ])
@section('content')
<div class="div-main d-none">
    {!!Form::open()
    ->post()
    ->route('frontbox.store')->id('form-pdv')
    !!}
    <div class="pl-lg-4">
        @include('front_box._forms2')
    </div>
    {!!Form::close()!!}

    @include('modals._novo_cliente')
</div>
@endsection


