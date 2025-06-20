@extends('layouts.app', ['title' => 'Novo Pagamento'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Pagamento</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('contadores.financeiro', [$contador->id]) }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('contadores.financeiro-store', [$contador->id])
        !!}
        <div class="pl-lg-4">
            @include('contadores._forms_finaceiro')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection


