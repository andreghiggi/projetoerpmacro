@extends('layouts.app', ['title' => 'Dias da semana'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>Dias de atendimento</h4>
                <hr>
                @can('atendimentos_create')
                <a class="btn btn-success px-3" href="{{ route('atendimentos.create') }}">
                    <i class="ri-add-circle-fill"></i>
                    Cadastrar
                </a>
                @endcan

                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-4">
                            {!!Form::select('funcionario_id', 'Funcionário', ['' => 'Selecione'] + $funcionarios->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>
                        
                        <div class="col-lg-4 col-12">
                            <br>

                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('atendimentos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Funcionário</th>
                                <th>Dias</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $item)
                            <tr>
                                <td data-label="Funcionário">{{ $item->funcionario->nome }}</td>
                                <td data-label="Dias">{{ $item->diaStr() }}</td>
                                <td>
                                    <form style="width: 100px;" action="{{ route('atendimentos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                        @csrf
                                        @method('delete')

                                        @can('atendimentos_edit')
                                        <a class="btn btn-warning btn-sm" href="{{ route('atendimentos.edit', [$item->id]) }}">
                                            <i class="ri-pencil-fill"></i>
                                        </a>
                                        @endcan

                                        @can('atendimentos_delete')
                                        <button type="submit" title="Deletar" class="btn btn-danger btn-sm btn-delete">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Nada encontrado</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
