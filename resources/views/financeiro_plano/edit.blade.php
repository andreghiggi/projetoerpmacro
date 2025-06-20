@extends('layouts.app', ['title' => 'Editar Pagamento'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Editar Pagamento</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('financeiro-plano.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('financeiro-plano.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('financeiro_plano._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
