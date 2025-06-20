@extends('layouts.app', ['title' => 'Empresas do Contador'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Empresas do Contador <strong class="text-primary">{{ $item->nome }}</strong></h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('contadores.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <form class="row" method="post" action="{{ route('contadores.add-business', [$item->id]) }}">
            @csrf
            @method('put')
            <div class="col-md-4">
                {!!Form::select('empresa_contador_id', 'Empresa')
                ->attrs(['class' => 'form-control'])
                ->required()
                !!}
            </div>
            <div class="col-md-2">
                <br>
                <button class="btn btn-success">
                    <i class="ri-add-circle-line"></i>
                    Adicionar
                </button>
            </div>
        </form>

        <div class="row mt-3">
            <div class="table-responsive">
                <table class="table table-striped table-centered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Razão Social</th>
                            <th>CPF/CNPJ</th>
                            <th>Data de cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->empresasAtribuidas as $e)
                        <tr>
                            <td>{{ $e->empresa->nome }}</td>
                            <td>{{ $e->empresa->cpf_cnpj }}</td>
                            <td>{{ __data_pt($e->empresa->created_at) }}</td>
                            <td>
                                <form action="{{ route('contadores.destroy-business', $e->id) }}" method="post" id="form-{{$e->id}}">
                                    @method('delete')
                                    @csrf
                                    <button type="button" class="btn btn-delete btn-sm btn-danger">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


