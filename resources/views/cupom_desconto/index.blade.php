@extends('layouts.app', ['title' => 'Cupons de Desconto'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <div class="">
                        <a href="{{ route('cupom-desconto.create') }}" class="btn btn-success">
                            <i class="ri-add-circle-fill"></i>
                            Novo Cupom
                        </a>
                    </div>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('cliente_id', 'Pesquisar por cliente')
                            ->options($cliente != null ? [$cliente->id => $cliente->razao_social] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('data_expiracao', 'Data de expiração')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('status', 'Status', ['' => 'Todos', '1' => 'Ativo', '0' => 'Desativados'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('cupom-desconto.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Valor</th>
                                    <th>Tipo de desconto</th>
                                    <th>Cliente</th>
                                    <th>Ativo</th>
                                    <th>Valor minímo de pedido</th>
                                    <th>Expiração</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>{{ $item->codigo }}</td>
                                    <td>{{ __moeda($item->valor) }}</td>
                                    <td>{{ strtoupper($item->tipo_desconto) }}</td>
                                    <td>{{ $item->cliente ? $item->cliente->razao_social : 'Todos' }}</td>
                                    <td>
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>R$ {{ __moeda($item->valor_minimo_pedido)}}</td>
                                    <td>{{ __data_pt($item->expiracao, 0) }}</td>
                                    <td>
                                        <form action="{{ route('cupom-desconto.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            <a class="btn btn-warning btn-sm" href="{{ route('cupom-desconto.edit', [$item->id]) }}">
                                                <i class="ri-pencil-fill"></i>
                                            </a>

                                            @csrf
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
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
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection