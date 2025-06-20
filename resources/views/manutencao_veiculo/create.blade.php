@extends('layouts.app', ['title' => 'Nova Manutenção'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Manutenção</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('manutencao-veiculos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('manutencao-veiculos.store')
        !!}
        <div class="pl-lg-4">
            @include('manutencao_veiculo._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>

@endsection

@section('js')
<script src="/js/manutencao_veiculo.js"></script>

@endsection
