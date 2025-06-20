@extends('layouts.app', ['title' => 'Lista de Trocas'])

@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        @can('troca_create')
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-nova-troca">
                            <i class="ri-add-circle-fill"></i>
                            Nova Troca
                        </button>
                        @endcan
                    </div>
                    <div class="col-md-8"></div>

                </div>
                
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-4">
                            {!!Form::select('cliente_id', 'Cliente')
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
                        

                        <div class="col-md-4">
                            <br>

                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('trocas.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-lg-12 mt-4">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>

                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Código</th>
                                    <th>Valor da troca</th>
                                    <th>Valor da venda</th>
                                    <th>Data da troca</th>
                                    <th>Venda ID</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>{{ $item->numero_sequencial }}</td>
                                    <td><label style="width: 400px;">{{ $item->nfce->cliente ? $item->nfce->cliente->razao_social : "--" }}</label></td>
                                    
                                    <td>{{ $item->codigo }}</td>
                                    <td>{{ number_format($item->valor_troca, 2, ',', '.') }}</td>
                                    <td>{{ number_format($item->valor_original, 2, ',', '.') }}</td>

                                    <td><label style="width: 120px">{{ __data_pt($item->created_at) }}</label></td>
                                    <td>{{ $item->nfce ? $item->nfce->numero_sequencial : '' }}</td>
                                    
                                    <td>
                                        <form action="{{ route('trocas.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 320px">
                                            @method('delete')
                                            @csrf

                                            @can('troca_delete')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan

                                            <a class="btn btn-ligth btn-sm" title="Detalhes" href="{{ route('trocas.show', $item->id) }}"><i class="ri-eye-line"></i></a>

                                            <a target="_blank" class="btn btn-dark btn-sm" title="Detalhes" href="{{ route('trocas.imprimir', $item->id) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-nova-troca" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="get" action="{{ route('trocas.create') }}">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nova troca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12">
                            {!!Form::text('codigo', 'Código da venda')!!}
                        </div>

                        <h4 class="mt-2">OU</h4>

                        <div class="col-md-12">
                            {!!Form::text('numero_nfce', 'Número NFCe')!!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Procurar</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection


