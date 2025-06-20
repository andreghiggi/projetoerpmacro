@extends('layouts.app', ['title' => 'Editar Conta Receber'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h3>Editar Conta Receber</h3>
        <div style="text-align: right;" class="">
            <a href="{{ route('conta-receber.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('conta-receber.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('conta-receber._forms', ['edit' => true])
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
