@extends('layouts.app', ['title' => 'Planejamento de Custo'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    @can('planejamento_custo_create')
                    <a href="{{ route('planejamento-custo.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Planejamento
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-2">
                       <!--  <div class="col-md-4">
                            {!!Form::select('cliente_id', 'Cliente')
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div> -->

                        <div class="col-md-2">
                            {!!Form::select('projeto_id', 'Projeto', ['' => 'Selecione'] + $projetos->pluck('_id', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('estado', 'Estado',
                            ['novo' => 'Novo',
                            'orcamento' => 'Orçamento',
                            'proposta' => 'Proposta',
                            'cotacao' => 'Cotação',
                            'finalizado' => 'Finalizado',
                            'cancelado' => 'Cancelado',
                            '' => 'Todos'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-2 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('planejamento-custo.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Projeto</th>
                                    <th>Estado</th>
                                    <th>Data de cadastro</th>
                                    <th>Usuário</th>
                                    <th>Total de produtos</th>
                                    <th>Total de serviços</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="#">{{ $item->numero_sequencial }}</td>
                                    <td data-label="Projeto">{{ $item->projeto ? $item->projeto->_id : '--' }}</td>
                                    <td data-label="Estado">{!! $item->_estado() !!}</td>
                                    <td data-label="Data de cadastro">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Usuário">{{ $item->usuario->name }}</td>
                                    <td data-label="Total de produtos">{{ sizeof($item->produtos) }}</td>
                                    <td data-label="Total de serviços">{{ sizeof($item->servicos) }}</td>
                                    <td>
                                        <form action="{{ route('planejamento-custo.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 200px;">
                                            <a title="Detalhes do planejamento" class="btn btn-dark btn-sm" href="{{ route('planejamento-custo.show', [$item->id]) }}">
                                                <i class="ri-clipboard-line"></i>
                                            </a>

                                            @if($item->estado != 'finalizado')
                                            @method('delete')
                                            @can('planejamento_custo_edit')
                                            <a title="Editar" class="btn btn-warning btn-sm" href="{{ route('planejamento-custo.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @endif

                                            @if($item->estado != 'finalizado')
                                            @can('planejamento_custo_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                            @endif
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


