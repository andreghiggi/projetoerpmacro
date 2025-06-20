@extends('front_box.default', ['title' => 'Editar Venda - PDV'])
@section('content')

{!!Form::open()->fill($item)
->put()
->route('frontbox.update', [$item->id])
->id('form-pdv-update')
->multipart()
!!}
<div class="pl-lg-4">
    @include('front_box._forms')
</div>
{!!Form::close()!!}

@endsection

{{-- @section('js')
<script type="text/javascript" src="/js/frente_caixa.js"></script>
@endsection --}}
