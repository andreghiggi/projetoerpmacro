@extends('layouts.app', ['title' => 'Funcionários'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @can('funcionario_create')
                    <div class="col-md-2 mt-1">
                        <a href="{{ route('funcionarios.create') }}" class="btn btn-success">
                            <i class="ri-add-circle-fill"></i>
                            Novo Funcionário
                        </a>
                    </div>

                    <div class="col-md-7"></div>
                    <div class="col-md-3 mt-1">
                        <a href="{{ route('comissao.index') }}" class="btn btn-dark float-end">
                            <i class="ri-wallet-2-fill"></i>
                            Comissão de vendas
                        </a>
                    </div>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-4">
                            {!!Form::text('nome', 'Pesquisar por nome')
                            !!}
                        </div>
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('funcionarios.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nome</th>
                                    <th>Salário</th>
                                    <th>Comissão</th>
                                    <th>Data de cadastro</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Nome">{{ $item->nome }}</td>
                                    <td data-label="Salário">{{ __moeda($item->salario) }}</td>
                                    <td data-label="Comissão">{{ __moeda($item->comissao) }}</td>
                                    <td data-label="Data de cadastro">{{ __data_pt($item->created_at, 1) }}</td>
                                    <td data-label="Status">
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <form style="width: 150px;" action="{{ route('funcionarios.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('funcionario_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('funcionarios.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan

                                            @csrf
                                            @can('funcionario_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan

                                            @can('funcionario_edit')
                                            <a href="{{ route('funcionarios.atribuir', [$item->id]) }}" class="btn btn-dark btn-sm" title="Atribuir Funcionário a Serviço">
                                                <i class="ri-user-settings-fill"></i>
                                            </a>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>

                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection