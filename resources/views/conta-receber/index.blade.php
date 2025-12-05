@extends('layouts.app', ['title' => 'Contas a Receber'])
@section('css')
<style type="text/css">
    .badge:hover{
        cursor: pointer;
    }
</style>
@endsection
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('conta_receber_create')
                    <a href="{{ route('conta-receber.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Conta Receber
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-2">
                        <div class="col-md-4">
                            {!!Form::select('cliente_id', 'Pesquisar por nome')->attrs(['class' => 'select2'])
                            ->options($cliente != null ? [$cliente->id => $cliente->info] : [])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('filtro_data', 'Filtro de data', ['data_vencimento' => 'Data de vencimento', 'data_recebimento' => 'Data de recebimento', 'created_at' => 'Data de cadastro'])
                            ->attrs(['class' => 'form-select'])
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
                        @if(__countLocalAtivo() > 1)
                        <div class="col-md-2">
                            {!!Form::select('local_id', 'Local', ['' => 'Selecione'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>
                        @endif
                        <div class="col-md-2">
                            {!!Form::select('status', 'Status', ['' => 'Todas', 1 => 'Recebidas', 0 => 'Pendentes', -1 => 'Vencidas'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('ordem', 'Ordenar por', ['' => 'Data de cadastro', 1 => 'Data de vencimento'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('categoria_conta_id', 'Categoria', ['' => 'Todas']+$categorias->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-4 col-xl-2 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('conta-receber.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                     <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                @can('conta_receber_delete')
                                <th>
                                    <div class="form-check form-checkbox-danger mb-2">
                                        <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                    </div>
                                </th>
                                @endcan
                                <th data-label="Cliente">Cliente</th>
                                <th data-label="Descrição">Descrição</th>
                                @if(__countLocalAtivo() > 1)
                                <th data-label="Local">Local</th>
                                @endif
                                <th data-label="Categoria">Categoria</th>
                                <th data-label="Valor Integral">Valor Integral</th>
                                <th data-label="Valor Recebido">Valor Recebido</th>
                                <th data-label="Data Cadastro">Data Cadastro</th>
                                <th data-label="Data Vencimento">Data Vencimento</th>
                                <th data-label="Data Recebimento">Data Recebimento</th>
                                <th data-label="Estado">Estado</th>
                                <th data-label="Venda">Venda</th>
                                <th width="10%">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $item)
                            <tr>
                                @can('conta_receber_delete')
                                <td>
                                    <div class="form-check form-checkbox-danger mb-2">
                                        <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
                                    </div>
                                </td>
                                @endcan

                                <td data-label="Cliente"><label style="width:400px">{{ $item->cliente ? $item->cliente->razao_social : '--' }}</label></td>
                                <td data-label="Descrição"><label style="width:120px">{{ $item->descricao }}</label></td>

                                @if(__countLocalAtivo() > 1)
                                <td data-label="Local" class="text-danger">{{ $item->localizacao->descricao }}</td>
                                @endif

                                <td data-label="Categoria">{{ $item->categoria ? $item->categoria->nome : '--' }}</td>
                                <td data-label="Valor Integral">{{ __moeda($item->valor_integral) }}</td>
                                <td data-label="Valor Recebido">{{ __moeda($item->valor_recebido) }}</td>
                                <td data-label="Data Cadastro">{{ __data_pt($item->created_at, 0) }}</td>

                                <td data-label="Data Vencimento">
                                    {{ __data_pt($item->data_vencimento, 0) }}
                                    @if(!$item->status)
                                    <br><span class="text-danger" style="font-size:10px">{{ $item->diasAtraso() }}</span>
                                    @endif
                                </td>

                                <td data-label="Data Recebimento">{{ $item->status ? __data_pt($item->data_recebimento, false) : '--' }}</td>

                                <td data-label="Estado">
                                    @if($item->status)
                                    <span class="btn btn-success btn-sm" style="width:120px"><i class="ri-checkbox-line"></i> Recebido</span>
                                    @else
                                    @if(strtotime($item->data_vencimento) < strtotime(date('Y-m-d')))
                                    <span class="btn btn-danger btn-sm" style="width:120px"><i class="ri-alert-line"></i> Em atraso</span>
                                    @else
                                    <span class="btn btn-warning btn-sm" style="width:120px"><i class="ri-alert-line"></i> Pendente</span>
                                    @endif
                                    @if($item->motivo_estorno)
                                    <span onclick="motivoEstorno('{{ $item->motivo_estorno }}')" class="badge bg-primary">estornada</span>
                                    @endif
                                    @endif
                                </td>

                                <td data-label="Venda">
                                    @if($item->nfce)
                                    <a href="{{ route('nfce.show', [$item->nfce->id]) }}" class="btn btn-sm btn-primary">PDV</a>
                                    #{{ $item->nfce->numero_sequencial }}
                                    @elseif($item->nfe)
                                    <a href="{{ route('nfe.show', [$item->nfe->id]) }}" class="btn btn-sm btn-dark">Pedido</a>
                                    #{{ $item->nfe->numero_sequencial }}

                                    @elseif($item->ordemServico)
                                    <a href="{{ route('ordem-servico.show', [$item->ordemServico->id]) }}" class="btn btn-sm btn-dark">OS</a>
                                    #{{ $item->ordemServico->codigo_sequencial }}
                                    @else
                                    --
                                    @endif
                                </td>

                                <td>
                                    <form action="{{ route('conta-receber.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width:250px">
                                        @csrf
                                        @if(!$item->status)
                                        @method('delete')
                                        @can('conta_receber_edit')
                                        <a class="btn btn-warning btn-sm" href="{{ route('conta-receber.edit', [$item->id]) }}"><i class="ri-pencil-fill"></i></a>
                                        @endcan
                                        @can('conta_receber_delete')
                                        <button type="button" class="btn btn-delete btn-sm btn-danger"><i class="ri-delete-bin-line"></i></button>
                                        @endcan
                                        @can('conta_receber_edit')
                                        <a title="Receber conta" href="{{ route('conta-receber.pay', $item) }}" class="btn btn-success btn-sm text-white">
                                            <i class="ri-money-dollar-box-line"></i>
                                        </a>
                                        @endcan
                                        @else
                                        @if(!$item->motivo_estorno)
                                        <a title="Estornar conta" href="{{ route('conta-receber.estornar', $item) }}" class="btn btn-info btn-sm text-white">
                                            <i class="ri-arrow-go-back-fill"></i>
                                        </a>
                                        @endif
                                        @endif

                                        @if(!$item->boleto && !$item->status)
                                        @can('boleto_create')
                                        <a title="Gerar boleto" class="btn btn-dark btn-sm" href="{{ route('boleto.create', [$item->id]) }}">
                                            <i class="ri-file-list-2-line"></i>
                                        </a>
                                        @endcan
                                        @elseif($item->boleto)
                                        @can('boleto_view')
                                        <a title="Visualizar boleto" class="btn btn-dark btn-sm" href="{{ route('boleto.show', [$item->id]) }}">
                                            <i class="ri-file-list-3-fill"></i>
                                        </a>
                                        @endcan
                                        @endif

                                        @if($item->status)
                                        <a title="Imprimir comprovante" class="btn btn-dark btn-sm" target="_blank" href="{{ route('conta-receber.imprimir-comprovante', [$item->id]) }}">
                                            <i class="ri-printer-line"></i>
                                        </a>
                                        <a title="Ver conta" class="btn btn-light btn-sm" href="{{ route('conta-receber.show', [$item->id]) }}">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        @endif

                                        @if($item->arquivo)
                                        <a title="Baixar arquivo" class="btn btn-dark btn-sm" href="{{ route('conta-receber.download-file', [$item->id]) }}">
                                            <i class="ri-attachment-line"></i>
                                        </a>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center">Nada encontrado</td>
                            </tr>
                            @endforelse
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="4">Soma da página</td>
                                <td>{{ __moeda($data->sum('valor_integral')) }}</td>
                                <td>{{ __moeda($data->sum('valor_recebido')) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                </div>
                <br>
                <div class="row">
                    <div class="col-md-2">
                        @can('conta_receber_delete')
                        <form action="{{ route('conta-receber.destroy-select') }}" method="post" id="form-delete-select">
                            @method('delete')
                            @csrf
                            <div></div>
                            <button type="button" class="btn btn-danger btn-sm btn-delete-all w-100" disabled>
                                <i class="ri-close-circle-line"></i> Remover selecionados
                            </button>
                        </form>
                        @endcan
                    </div>

                    <div class="col-md-2">
                        @can('conta_receber_edit')
                        <form action="{{ route('conta-receber.recebe-select') }}" method="post" id="form-recebe-paga-select">
                            @csrf
                            <div></div>
                            <button class="btn btn-success btn-sm w-100 btn-recebe-paga-all" disabled>
                                <i class="ri-check-line"></i> Receber selecionados
                            </button>
                        </form>
                        @endcan
                    </div>

                    <div class="col-md-2 text-end">
                        @if(request()->has('categoria_conta_id'))
                        <form action="{{ route('conta-receber.export-excel') }}" method="get">
                            @foreach(request()->all() as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <button type="submit" class="btn btn-dark btn-sm w-100">
                                <i class="ri-file-excel-line "></i> Exportar para Excel
                            </button>
                        </form>
                        @endif

                    </div>

                    <div class="col-md-6 text-end">
                        @can('boleto_create')
                        <form action="{{ route('boleto.create-several') }}" method="get" id="form-gerar-boletos">
                            <div></div>
                            <button type="submit" class="btn btn-dark btn-sm btn-boleto" disabled>
                                <i class="ri-file-line"></i> Gerar boletos
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
            <br>
            {!! $data->appends(request()->all())->links() !!}
        </div>
    </div>
</div>
</div>
@endsection
@section('js')
<script type="text/javascript" src="/js/delete_selecionados.js"></script>
<script type="text/javascript" src="/js/boleto.js"></script>
<script type="text/javascript" src="/js/recebe_paga_selecionados.js"></script>

<script type="text/javascript">
    function motivoEstorno(motivo) {
        swal("", motivo, 'info')
    }
</script>

@endsection
