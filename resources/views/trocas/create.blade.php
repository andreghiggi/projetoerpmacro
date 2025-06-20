@extends('front_box.default', ['title' => 'Nova Troca' ])
@section('content')

{!!Form::open()
->post()
->route('trocas.store')->id('form-troca')
!!}
<div class="pl-lg-4">
    @include('trocas._forms')
</div>
{!!Form::close()!!}
@include('modals._novo_cliente')

@endsection
