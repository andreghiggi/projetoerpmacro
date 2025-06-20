@extends('layouts.app', ['title' => 'Produtos'])
@section('css')
<style type="text/css">
    .div-overflow {
        width: 180px;
        overflow-x: auto;
        white-space: nowrap;
    }
</style>
@endsection
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    @can('produtos_create')
                    <a href="{{ route('produtos.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo Produto
                    </a>
                    @endcan

                    <a href="{{ route('produtos.import') }}" class="btn btn-info pull-right">
                        <i class="ri-file-upload-line"></i>
                        Upload
                    </a>

                    <a href="{{ route('produtos.export') }}" class="btn btn-success pull-right">
                        <i class="ri-file-excel-line"></i>
                        Exportar
                    </a>
                    @can('produtos_edit')
                    <a href="{{ route('produtos.reajuste') }}" class="btn btn-dark pull-right">
                        <i class="ri-file-edit-fill"></i>
                        Reajuste em Grupo
                    </a>
                    @endif

                    <a href="{{ route('produtos.upload-imagens') }}" class="btn btn-light pull-right">
                        <i class="ri-image-fill"></i>
                        Upload de Imagens
                    </a>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-1">
                        <div class="col-md-2">
                            {!!Form::text('nome', 'Pesquisar por nome')
                            !!}
                        </div>
                        
                        <div class="col-md-2">
                            {!!Form::tel('codigo_barras', 'Pesquisar código de barras')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('tipo', 'Tipo', ['' => 'Todos', 'composto' => 'Composto', 'variavel' => 'Variável', 'combo' => 'Combo'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('categoria_id', 'Categoria', ['' => 'Todos'] + $categorias->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Dt. inicial cadastro')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Dt. final cadastro')
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
                            {!!Form::select('com_variacao', 'Com variação', ['' => 'Todos', 1 => 'Sim', 0 => 'Não'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('ordem', 'Ordenar por', ['nome' => 'Nome', 'numero_sequencial' => 'Código', 'created_at' => 'Data de cadastro'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('produtos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <h6>Total de registros: <strong>{{ $data->total() }}</strong></h6>
                    <h6>Total de produtos cadastrados: <strong>{{ $totalCadastros }}</strong></h6>
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    @can('produtos_delete')
                                    <th>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                        </div>
                                    </th>
                                    @endcan
                                    <th>Ações</th>
                                    <th>Imagem</th>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Valor de venda</th>
                                    <th>Valor de compra</th>
                                    @if(__countLocalAtivo() > 1)
                                    <th>Disponibilidade</th>
                                    @endif
                                    <th>Categoria</th>
                                    <th>Código de barras</th>
                                    <th>NCM</th>
                                    <th>Unidade</th>
                                    <th>Data de cadastro</th>
                                    <th>CFOP</th>
                                    <th>Gerenciar estoque</th>
                                    @can('estoque_view')
                                    <th>Estoque</th>
                                    @endcan
                                    <th>Status</th>
                                    <th>Variação</th>
                                    <th>Combo</th>
                                    @if(__isActivePlan(Auth::user()->empresa, 'Cardapio'))
                                    <th>Cardápio</th>
                                    @endif
                                    @if(__isActivePlan(Auth::user()->empresa, 'Delivery'))
                                    <th>Delivery</th>
                                    @endif
                                    @if(__isActivePlan(Auth::user()->empresa, 'Ecommerce'))
                                    <th>Ecommerce</th>
                                    @endif
                                    @if(__isActivePlan(Auth::user()->empresa, 'Reservas'))
                                    <th>Reserva</th>
                                    @endif
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    @can('produtos_delete')
                                    <td>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
                                        </div>
                                    </td>
                                    @endcan
                                    <td>
                                        <form style="width: 330px" action="{{ route('produtos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @can('produtos_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('produtos.edit', [$item->id]) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @endcan
                                            @csrf
                                            @can('produtos_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan

                                            @if($item->composto == true)
                                            <a class="btn btn-info btn-sm" href="{{ route('produto-composto.show', [$item->id]) }}" title="Ver composição"><i class="ri-search-eye-fill"></i></a>
                                            @endif

                                            @if($item->alerta_validade != '')
                                            <a title="Ver lote e vencimento" type="button" class="btn btn-light btn-sm" onclick="infoVencimento('{{$item->id}}')" data-bs-toggle="modal" data-bs-target="#info_vencimento"><i class="ri-eye-line"></i></a>
                                            @endif

                                            <a title="Ver movimentações" href="{{ route('produtos.show', [$item->id]) }}" class="btn btn-dark btn-sm"><i class="ri-draft-line"></i></a>

                                            <a class="btn btn-primary btn-sm" href="{{ route('produtos.duplicar', [$item->id]) }}" title="Duplicar produto">
                                                <i class="ri-file-copy-line"></i>
                                            </a>
                                            <a class="btn btn-light btn-sm" href="{{ route('produtos.etiqueta', [$item->id]) }}" title="Gerar etiqueta">
                                                <i class="ri-barcode-box-line"></i>
                                            </a>

                                            @if(__countLocalAtivo() > 1)
                                            <a class="btn btn-dark btn-sm" href="{{ route('produto-tributacao-local.index', [$item->id]) }}" title="Valores por local">
                                                <i class="ri-percent-fill"></i>
                                            </a>
                                            @endif
                                        </form>
                                    </td>
                                    <td><img class="img-60" src="{{ $item->img }}"></td>
                                    <td style="font-weight: bold;">{{ $item->numero_sequencial }}</td>
                                    <td><label style="width: 300px">{{ $item->nome }}</label></td>
                                    @if(sizeof($item->variacoes) > 0)
                                    <td>
                                        <div class="div-overflow">
                                            {{ $item->valoresVariacao() }}
                                        </div>
                                    </td>
                                    @else
                                    <td><label style="width: 100px">{{ __moeda($item->valor_unitario) }}</label></td>
                                    @endif
                                    <td><label style="width: 120px">{{ __moeda($item->valor_compra) }}</label></td>
                                    @if(__countLocalAtivo() > 1)
                                    <td>
                                        <label style="width: 250px">
                                            @foreach($item->locais as $l)
                                            @if($l->localizacao)
                                            <strong>{{ $l->localizacao->descricao }}</strong>
                                            @if(!$loop->last) | @endif
                                            @endif
                                            @endforeach
                                        </label>
                                    </td>
                                    @endif
                                    <td width="150">{{ $item->categoria ? $item->categoria->nome : '--' }}</td>
                                    <td width="200">{{ $item->codigo_barras ?? '--' }}</td>
                                    <td>{{ $item->ncm }}</td>
                                    <td>{{ $item->unidade }}</td>
                                    <td>{{ __data_pt($item->created_at) }}</td>
                                    <td>{{ $item->cfop_estadual }}/{{ $item->cfop_outro_estado }}</td>
                                    <td>
                                        @if($item->gerenciar_estoque)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>

                                    @can('estoque_view')
                                    <td>
                                        <label style="width: 200px">
                                            @if(__countLocalAtivo() == 1)
                                            {{ $item->estoqueAtual() }}
                                            @else
                                            

                                            @foreach($item->estoqueLocais as $e)
                                            @if($e->local)
                                            {{ $e->local->descricao }}:
                                            <strong class="text-success">
                                                @if(!$item->unidadeDecimal())
                                                {{ number_format($e->quantidade, 0) }}
                                                @else
                                                {{ number_format($e->quantidade, 3) }}
                                                @endif
                                            </strong>
                                            @endif
                                            @if(!$loop->last) | @endif
                                            @endforeach

                                            @endif
                                        </label>
                                        
                                    </td>
                                    @endcan

                                    <td>
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if(sizeof($item->variacoes) > 0)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->combo)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    @if(__isActivePlan(Auth::user()->empresa, 'Cardapio'))
                                    <td>
                                        @if($item->cardapio)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    @endif
                                    @if(__isActivePlan(Auth::user()->empresa, 'Delivery'))
                                    <td>
                                        @if($item->delivery)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    @endif
                                    @if(__isActivePlan(Auth::user()->empresa, 'Ecommerce'))
                                    <td>
                                        @if($item->ecommerce)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    @endif
                                    @if(__isActivePlan(Auth::user()->empresa, 'Reservas'))
                                    <td>
                                        @if($item->reserva)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    @endif
                                    
                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="21" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                @can('produtos_delete')
                <form action="{{ route('produtos.destroy-select') }}" method="post" id="form-delete-select">
                    @method('delete')
                    @csrf
                    <div></div>
                    <button type="button" class="btn btn-danger btn-sm btn-delete-all" disabled>
                        <i class="ri-close-circle-line"></i> Remover selecionados
                    </button>
                </form>
                @endcan
                <br>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>

@include('modals._info_vencimento', ['not_submit' => true])

@endsection

@section('js')
<script type="text/javascript" src="/js/delete_selecionados.js"></script>

<script type="text/javascript">
    function infoVencimento(id) {
        $.get(path_url + 'api/produtos/info-vencimento/' + id)
        .done((res) => {
            $('.table-infoValidade tbody').html(res)
        })
        .fail((e) => {
            console.log(e)
        })
    }

</script>
@endsection
