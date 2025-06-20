@extends('layouts.app', ['title' => 'Editar Empresa'])
@section('content')
<div class="card mt-1"> 
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('config.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('config.configuracao')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
