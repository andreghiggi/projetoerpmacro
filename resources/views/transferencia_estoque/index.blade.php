@extends('layouts.app', ['title' => 'Transferências de estoque'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    @can('transferencia_estoque_create')
                    <a href="{{ route('transferencia-estoque.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Transferência
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-1">
                        <div class="col-md-3">
                            {!!Form::text('produto', 'Pesquisar por produto')
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
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('transferencia-estoque.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Local de saída</th>
                                    <th>Local de entrada</th>
                                    <th>Data</th>
                                    <th>Usuário</th>
                                    <th>Observação</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="#"> {{ $item->codigo_transacao }}</td>
                                    <td data-label="Local de saída">{{ $item->local_saida->descricao }}</td>
                                    <td data-label="Local de entrada">{{ $item->local_entrada->descricao }}</td>
                                    <td data-label="Data">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Usuário">{{ $item->usuario->name }}</td>
                                    <td data-label="Observação">{{ $item->observacao }}</td>

                                    <td>
                                        <form action="{{ route('transferencia-estoque.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <a class="btn btn-dark btn-sm" target="_blank" href="{{ route('transferencia-estoque.imprimir', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>

                                            @can('transferencia_estoque_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nada encontrado</td>
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


