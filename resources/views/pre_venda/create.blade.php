@extends('front_box.default', ['title' => 'PRÉ VENDA'])
@section('content')

{!! Form::open()
->post()
->route('pre-venda.store')->id('form-prevenda') !!}
<div class="">
    @include('pre_venda._forms')
</div>
{!! Form::close() !!}
@include('modals._novo_cliente')

@endsection
