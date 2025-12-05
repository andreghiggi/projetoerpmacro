@extends('front_box.default', ['title' => 'EDITAR PRÉ VENDA'])
@section('content')

{!! Form::open()
->put()
->route('pre-venda.update', [$item->id])->id('form-prevenda') !!}
<div class="">
    @include('pre_venda._forms')
</div>
{!! Form::close() !!}
@include('modals._novo_cliente')

@endsection
