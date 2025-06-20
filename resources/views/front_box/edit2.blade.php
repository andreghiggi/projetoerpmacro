@extends('front_box.default', ['title' => 'Editar Venda - PDV'])
@section('content')

{!!Form::open()->fill($item)
->put()
->route('frontbox.update', [$item->id])
->id('form-pdv-update')

!!}
<div class="pl-lg-4">
    @include('front_box._forms2')
</div>
{!!Form::close()!!}

@endsection

