@extends('layouts.app', ['title' => 'Editar NFSe'])
@section('content')

<div class="card mt-1">
    <div class="card-header">

        <h4>Editar NFSe</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('nota-servico.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('nota-servico.update', [$item->id])

        !!}
        <div class="pl-lg-4">
            @include('nota_servico._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')
<script src="/js/nfse.js"></script>
@endsection
