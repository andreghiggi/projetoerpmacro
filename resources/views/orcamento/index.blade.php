@extends('layouts.app', ['title' => 'Orçamentos'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @can('orcamento_create')
                    <div class="col-md-2">
                        <a href="{{ route('nfe.create', ['orcamento=1']) }}" class="btn btn-success">
                            <i class="ri-add-circle-fill"></i>
                            Novo Orçamento
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

                        <div class="col-md-3">
                            {!!Form::select('funcionario_id', 'Funcionário', ['' => 'Todos'] + $funcionarios->pluck('nome', 'id')->all())
                            ->id('funcionario')
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        
                        <div class="col-lg-4 col-12">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('orcamentos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>CPF/CNPJ</th>
                                    <!-- <th>Número</th> -->
                                    <th>Valor</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->numero_sequencial }}</td>
                                    <td>{{ $item->cliente ? $item->cliente->razao_social : "--" }}</td>
                                    <td>{{ $item->cliente ? $item->cliente->cpf_cnpj : "--" }}</td>
                                    <!-- <td>{{ $item->numero ? $item->id : '' }}</td> -->
                                    <td>{{ __moeda($item->total) }}</td>
                                    <td>{{ __data_pt($item->created_at) }}</td>
                                    <td width="300">
                                        <form action="{{ route('orcamentos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf
                                            <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('orcamentos.imprimir', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                            @can('orcamento_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('orcamentos.edit', $item->id) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @endcan
                                            @can('orcamento_delete')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete"><i class="ri-delete-bin-line"></i></button>
                                            @endcan

                                            @can('nfe_create')
                                            <a title="Gerar venda" class="btn btn-dark btn-sm" href="{{ route('orcamentos.show', $item->id) }}">
                                                <i class="ri-file-line"></i>
                                            </a>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {!! $data->appends(request()->all())->links() !!}
                </div>
                <h5>Soma: <strong class="text-success">R$ {{ __moeda($data->sum('total')) }}</strong></h5>

                @if(sizeof($data) > 0 && request()->cliente_id)
                <form method="get" action="{{ route('orcamentos.gerar-venda-multipla') }}">
                    @foreach($data as $item)
                    <input type="hidden" value="{{ $item->id }}" name="orcamento_id[]">
                    @endforeach
                    <button class="btn btn-dark" type="submit">
                        Gerar venda dos orçamentos
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
