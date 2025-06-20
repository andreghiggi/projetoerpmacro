@extends('layouts.app', ['title' => 'Ordem de Serviço'])
@section('content')

<div class="card mt-1">
    <div class="card-body">
        <div class="pl-lg-4">
            <div class="">

                <div class="ms">
                    <div class="row">
                        <div class="col-6">
                            <h5>Estado:
                                @if($ordem->estado == 'pd')
                                <span class="btn btn-warning btn-sm">PENDENTE</span>
                                @elseif($ordem->estado == 'ap')
                                <span class="btn btn-success btn-sm">APROVADO</span>
                                @elseif($ordem->estado == 'rp')
                                <span class="btn btn-danger btn-sm">REPROVADO</span>
                                @elseif($ordem->estado == 'fz')
                                <span class="btn btn-info btn-sm">FINALIZADO</span>
                                @endif
                            </h5> 
                        </div>
                        <div class="col-6">

                            <h3 class="text-danger">OS #{{ $ordem->codigo_sequencial }}</h3>
                        </div>
                    </div>

                    <div class="mt-" style="text-align: right;">
                        <a href="{{ route('ordem-servico.index') }}" class="btn btn-danger btn-sm px-3">
                            <i class="ri-arrow-left-double-fill"></i>Voltar
                        </a>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 col-6">
                            <h5>Início: <strong class="text-primary">{{ __data_pt($ordem->data_inicio) }}</strong></h5>
                        </div>


                        <div class="col-md-3 col-6">
                            <h5>Total: <strong class="text-primary">R$ {{ __moeda($ordem->valor) }}</strong> </h5>
                        </div>

                        <div class="col-md-3 col-6">
                            <h5>Atendente: <strong class="text-primary">{{ $ordem->funcionario ? $ordem->funcionario->nome : '' }}</strong></h5>
                        </div>

                        <div class="col-md-3 col-6">
                            <h5>Usuário: <strong class="text-primary">{{ $ordem->usuario->name }}</strong></h5>
                        </div>
                    </div>

                    <a href="{{ route('ordem-servico.alterar-estado', [$ordem->id]) }}" class="btn btn-info btn-sm" href=""><i class="ri-refresh-line"></i>
                        Alterar estado
                    </a>
                    <!-- <a target="_blank" class="btn btn-primary btn-sm" href="{{ route('ordem-servico.imprimir', $ordem->id) }}"><i class="ri-printer-line"></i>
                        Imprimir
                    </a> -->
                    <a target="_blank" class="btn btn-primary btn-sm" onclick="imprimir('{{ $ordem->id }}')"><i class="ri-printer-line"></i>
                        Imprimir
                    </a>
                    @if($ordem->nfe_id == 0)
                    <a class="btn btn-success btn-sm" href="{{ route('ordem-servico.gerar-nfe', $ordem->id) }}">
                        <i class="ri-file-text-line"></i>
                        Gerar NFe
                    </a>
                    @endif

                    @if($ordem->oticaOs)
                    <a class="btn btn-warning btn-sm" href="{{ route('ordem-servico.edit', $ordem->id) }}">
                        <i class="ri-edit-line"></i>
                        Editar receita
                    </a>
                    @endif
                    <!-- <h5 class="mt-2">NFSe:
                        @if($ordem->nfe_id)   
                        <strong>{{$ordem->nfe_id}}</strong>
                        @else
                        <strong> -- </strong>
                        @endif
                    </h5> -->
                </div>

                <form method="post" class="row g-2 mt-3" action="{{ route('ordem-servico.update-entrega', [$ordem->id])}}">
                    @csrf
                    @method('put')
                    <div class="col-md-2">
                        {!! Form::tel('adiantamento', 'Valor de adiantamento')->attrs(['class' => 'moeda'])
                        ->value(__moeda($ordem->adiantamento)) !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::date('data_entrega', 'Data de entrega')->value(substr($ordem->data_entrega,0, 10)) !!}
                    </div>
                    <div class="col-md-2">
                        <br>
                        <button class="btn btn-success" href="{{ route('ordem-servico.gerar-nfe', $ordem->id) }}">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
            <hr class="">
            <div class="card border row">
                {!! Form::open()
                ->post()
                ->route('ordem-servico.store-servico')!!}
                <h3 class="text-center mt-2">Serviços</h3>
                <div class="row m-2 mt-3">
                    <input type="hidden" value="{{$ordem->id}}" name="ordem_servico_id">
                    <div class="col-md-4">
                        {!! Form::select('servico_id', 'Serviço', [null => 'Selecione'] + $servicos->pluck('nome', 'id')->all())->attrs(['class' => 'form-select'])->required() !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::tel('quantidade', 'Quantidade')->attrs(['class' => 'moeda'])->required() !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::text('valor', 'Valor unitário')->attrs(['class' => 'moeda'])->required() !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::select('status', 'Status', [0 => 'Pendente', 1 => 'Finalizado'])->attrs(['class' => 'form-select'])->required() !!}
                    </div>
                    <div class="col-md-2">
                        <br>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-add-line"></i>Adicionar
                        </button>

                    </div>
                </div>
                {!! Form::close() !!}
                <div class="card-body">
                    <div class="table-responsive">
                        <p class="">total de serviços: <strong>{{ sizeof($ordem->servicos) }}</strong></p>
                        <table class="table mb-0 table-striped table-servico">
                            <thead class="table-dark">
                                <tr>
                                    <th>Serviço</th>
                                    <th>Quantidade</th>
                                    <th>Status</th>
                                    <th>Subtotal</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($ordem)
                                @forelse($ordem->servicos as $item)
                                <tr>
                                    <td>
                                        <input readonly type="text" name="servico[]" class="form-control" value="{{ $item->servico->nome }}">
                                    </td>
                                    <td>
                                        <input readonly type="tel" name="servico_quantidade[]" class="form-control" value="{{ $item->quantidade }}">
                                    </td>
                                    <td>
                                        @if($item->status)
                                        <span class="badge bg-success">FINALIZADO
                                        </span>
                                        @else
                                        <span class="badge bg-warning">PENDENTE
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <input readonly type="tel" name="valor[]" class="form-control qtd-item" value="{{ __moeda($item->subtotal) }}">
                                    </td>
                                    <td>
                                        <form action="{{ route('ordem-servico.deletar-servico', $item->id) }}" method="post" id="form-servico-{{$item->id}}">
                                            @method('delete')
                                            @csrf
                                            <a title="Alterar estado" href="{{ route('ordem-servico.alterar-status-servico', $item->id) }}" class="btn btn-sm btn-dark">
                                                <i class="ri-refresh-line"></i>
                                            </a>

                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Nenhum registro</td>
                                </tr>
                                @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr class="mt-3">
            <div class="card border row">
                {!! Form::open()
                ->post()
                ->route('ordem-servico.store-produto')!!}
                <h3 class="text-center mt-2">Produtos</h3>
                <div class="row m-2">
                    <input type="hidden" value="{{$ordem->id}}" name="ordem_servico_id">
                    <div class="col-md-4">
                        {!! Form::select('produto_id', 'Produto')->attrs(['class' => ''])->required() !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::tel('quantidade_produto', 'Quantidade')->attrs(['class' => 'moeda'])->required() !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::tel('valor_produto', 'Valor unitário')->attrs(['class' => 'moeda'])->required() !!}
                    </div>
                    <div class="col-md-2">
                        <br>
                        @if(!isset($not_submit))
                        <button type="submit" class="btn btn-success"><i class=" ri-add-line"></i>Adicionar</button>
                        @endif
                    </div>
                </div>
                {!! Form::close() !!}
                <div class="card-body">
                    <div class="table-responsive">
                        <p class="">total de produtos: <strong>{{ sizeof($ordem->itens) }}</strong></p>
                        <table class="table mb-0 table-striped table-produto">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Valor</th>
                                    <th>SubTotal</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($ordem)
                                @forelse ($ordem->itens  as $item)
                                <tr>
                                    <td>
                                        <input readonly type="text" name="produto[]" class="form-control" value="{{ $item->produto->nome }}">
                                    </td>
                                    <td>
                                        <input readonly type="tel" name="produto_quantidade[]" class="form-control" value="{{ $item->quantidade }}">
                                    </td>

                                    <td>
                                        <input readonly type="tel" name="total[]" class="form-control qtd-item" value="{{ __moeda($item->produto->valor_unitario) }}">
                                    </td>
                                    <td>
                                        <input readonly type="tel" name="subtotal[]" class="form-control qtd-item" value="{{ __moeda($item->subtotal) }}">
                                    </td>
                                    <td>
                                        <form action="{{ route('ordem-servico.deletar-produto', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            
                                            @csrf
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Nenhum registro</td>
                                </tr>
                                @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr class="mt-3">
            <div class="card border row">

                <h3 class="text-center mt-2">Relatórios:</h3>
                <div class="row m-2">
                    <div class="col-md-3">
                        <a href="{{ route('ordem-servico.add-relatorio', $ordem->id) }}" class="btn btn-success"><i class=" ri-add-line"></i>Adicionar relatório</a>
                    </div>
                    <p class="mt-2">total de relatórios: <strong>{{ sizeof($ordem->relatorios) }}</strong></p>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Data</th>
                                    <th>Usuário</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ordem->relatorios as $item)
                                <tr>
                                    <td>{{ __data_pt($item->created_at) }}</td>
                                    <td>{{ $item->usuario->name }}</td>
                                    <td>
                                        <form action="{{ route('ordem-servico.delete-relatorio', $item->id) }}" method="post" id="form-relatorio-{{$item->id}}">
                                            @method('delete')
                                            @csrf
                                            <a href="{{ route('ordem-servico.edit-relatorio', $item->id) }}" title="Editar" class="btn btn-warning btn-sm text-white">
                                                <i class="ri-pencil-fill"></i>
                                            </a>

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

            <div class="card border row">
                <h3 class="text-center mt-2">Descrição/Observação</h3>

                <div class="card-body">
                    <div class="col-md-12">
                        {!! $ordem->descricao !!}
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

@section('js')
<script type="text/javascript" src="/js/ordem_servico.js"></script>
@endsection
@endsection
