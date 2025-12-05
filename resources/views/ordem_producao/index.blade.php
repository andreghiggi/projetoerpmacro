@extends('layouts.app', ['title' => 'Ordens de Produção'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('ordem_producao_create')
                    <a href="{{ route('ordem-producao.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Ordem de Produção
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-2">
                        
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data de início')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data de fim')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('estado', 'Estado', ['' => 'Selecione', '1' => 'Com adiantamento', '-1' => 'Sem adiantamento'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('ordem-producao.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Observação</th>
                                    <th>Data de cadastro</th>
                                    <th>Data prevista de entrega</th>
                                    <th>Estado</th>
                                    <th>Funcionário</th>
                                    <th>Total de itens</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Código">{{ $item->codigo_sequencial }}</td>
                                    <td data-label="Observação">{{ $item->observacao }}</td>
                                    <td data-label="Data de cadastro">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Data prevista de entrega">{{ $item->data_prevista_entrega ? __data_pt($item->data_prevista_entrega, 0) : '' }}</td>
                                    <td data-label="Estado">
                                        @if($item->estado == 'novo')
                                        <span class="badge bg-secondary text-light">Novo</span>
                                        @elseif($item->estado == 'producao')
                                        <span class="badge bg-primary text-light">Produção</span>
                                        @elseif($item->estado == 'expedicao')
                                        <span class="badge bg-dark text-light">Expedição</span>
                                        @else
                                        <span class="badge bg-success text-light">Finalizado</span>
                                        @endif
                                    </td>
                                    <td data-label="Funcionário">{{ $item->funcionario ? $item->funcionario->nome : '' }}</td>
                                    <td data-label="Total de itens">{{ $item->itens->sum('quantidade') }}</td>

                                    <td>
                                        <form action="{{ route('ordem-producao.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf
                                            @can('ordem_producao_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('ordem-producao.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @can('ordem_producao_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                            <a title="Imprimir" href="{{ route('ordem-producao.show', $item->id) }}" class="btn btn-dark btn-sm text-white">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <br>
                        
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')

@endsection
