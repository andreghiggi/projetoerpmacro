@extends('layouts.app', ['title' => 'Novo Destaque'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Destaque</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('destaque-marketplace.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('destaque-marketplace.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('destaques._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
