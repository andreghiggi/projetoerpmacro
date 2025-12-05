@extends('layouts.app', ['title' => 'Gestão de Produção'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    @can('gestao_producao_create')
                    <a href="{{ route('gestao-producao.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Produção
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-2">
                        <div class="col-md-3">
                            {!!Form::select('cliente_id', 'Cliente')
                            ->attrs(['class' => 'select2'])
                            ->options($cliente ? [$cliente->id => $cliente->info] : [])
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::select('produto_id', 'Produto')
                            ->attrs(['class' => 'select2'])
                            ->options($produto ? [$produto->id => $produto->nome] : [])
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
                            {!!Form::select('status', 'Status',
                            [
                            '1' => 'Finalizado',
                            '0' => 'Pendente',

                            '' => 'Todos'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-2 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('gestao-producao.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
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
                                    <th>Cliente</th>
                                    <th>Produto</th>
                                    <th>Status</th>
                                    <th>Data de cadastro</th>
                                    <th>Usuário</th>
                                    <th>Total</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="#">{{ $item->numero_sequencial }}</td>
                                    <td data-label="Cliente">{{ $item->cliente ? $item->cliente->info : '--' }}</td>
                                    <td data-label="Produto">{{ $item->produto ? $item->produto->nome : '--' }}</td>
                                    <td data-label="Status">
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td data-label="Data de cadastro">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Usuário">{{ $item->usuario->name }}</td>
                                    <td data-label="Total">{{ __moeda($item->total_final) }}</td>

                                    <td>
                                        <form action="{{ route('gestao-producao.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 200px;">
                                            <a title="Detalhes da produção" class="btn btn-dark btn-sm" href="{{ route('gestao-producao.show', [$item->id]) }}">
                                                <i class="ri-clipboard-line"></i>
                                            </a>

                                            @if($item->status == 0)
                                            @method('delete')
                                            @can('gestao_producao_edit')
                                            <a title="Editar" class="btn btn-warning btn-sm" href="{{ route('gestao-producao.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('gestao_producao_delete')
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


