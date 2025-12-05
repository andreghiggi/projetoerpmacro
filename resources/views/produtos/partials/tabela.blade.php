<div class="col-md-12 mt-3">
    <h6>Total de registros: <strong>{{ $data->total() }}</strong></h6>
    <div class="table-responsive">
        <div class="tabela-scroll" style="overflow-x:auto;">

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
                        <th class="sticky-col first-col">Nome</th>
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
                        <td data-label="#">
                            <div class="form-check form-checkbox-danger mb-2">
                                <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
                            </div>
                        </td>
                        @endcan

                        <td class="text-start d-none d-md-table-cell">
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static">
                                    Ações
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <form action="{{ route('produtos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                        @method('delete')
                                        @csrf
                                        @can('produtos_edit')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('produtos.edit', [$item->id]) }}">
                                                <i class="ri-edit-line text-warning me-1"></i> Editar
                                            </a>
                                        </li>
                                        @endcan

                                        @can('produtos_delete')
                                        <li>
                                            <button class="dropdown-item text-danger btn-delete" data-id="{{ $item->id }}">
                                                <i class="ri-delete-bin-line me-1"></i> Excluir
                                            </button>
                                        </li>
                                        @endcan

                                        @if($item->composto == true)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('produto-composto.show', [$item->id]) }}">
                                                <i class="ri-search-eye-fill text-info me-1"></i> Ver composição
                                            </a>
                                        </li>
                                        @endif

                                        @if($item->alerta_validade != '')
                                        <li>
                                            <button class="dropdown-item" onclick="infoVencimento('{{ $item->id }}')" data-bs-toggle="modal" data-bs-target="#info_vencimento">
                                                <i class="ri-eye-line me-1"></i> Lote e validade
                                            </button>
                                        </li>
                                        @endif

                                        <li>
                                            <a class="dropdown-item" href="{{ route('produtos.show', [$item->id]) }}">
                                                <i class="ri-draft-line me-1"></i> Movimentações
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item" href="{{ route('produtos.duplicar', [$item->id]) }}">
                                                <i class="ri-file-copy-line text-primary me-1"></i> Duplicar
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item" href="{{ route('produtos.etiqueta', [$item->id]) }}">
                                                <i class="ri-barcode-box-line me-1"></i> Etiqueta
                                            </a>
                                        </li>

                                        @if(__countLocalAtivo() > 1)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('produto-tributacao-local.index', [$item->id]) }}">
                                                <i class="ri-percent-fill me-1"></i> Valores por local
                                            </a>
                                        </li>
                                        @endif
                                    </form>
                                </ul>
                            </div>
                        </td>


                        <td class="d-md-none">
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
                        <td data-label="Código" style="font-weight: bold;">{{ $item->numero_sequencial }}</td>
                        <td class="sticky-col first-col" data-label="Nome">
                            <label style="width: 300px">{{ $item->nome }}</label>
                            @if($item->local_armazenamento)
                            <br>
                            <label style="font-size: 11px; width: 300px">Local de armazenamento: <strong class="text-primary">{{ $item->local_armazenamento }}</strong></label>
                            @endif
                        </td>
                        @if($item->variacoes && sizeof($item->variacoes) > 0)
                        <td data-label="Valor de venda">
                            <div class="div-overflow">{{ $item->valoresVariacao() }}</div>
                        </td>
                        @else
                        <td data-label="Valor de venda"><label style="width: 100px">{{ __moeda($item->valor_unitario) }}</label></td>
                        @endif
                        <td data-label="Valor de compra"><label style="width: 120px">{{ __moeda($item->valor_compra) }}</label></td>
                        @if(__countLocalAtivo() > 1)
                        <td data-label="Disponibilidade">
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
                        <td data-label="Categoria">{{ $item->categoria ? $item->categoria->nome : '--' }}</td>
                        <td data-label="Código de barras">{{ $item->codigo_barras ?? '--' }}</td>
                        <td data-label="NCM">{{ $item->ncm }}</td>
                        <td data-label="Unidade">{{ $item->unidade }}</td>
                        <td data-label="Data de cadastro">{{ __data_pt($item->created_at) }}</td>
                        <td data-label="CFOP">{{ $item->cfop_estadual }}/{{ $item->cfop_outro_estado }}</td>
                        <td data-label="Gerenciar estoque">
                            @if($item->gerenciar_estoque)
                            <i class="ri-checkbox-circle-fill text-success"></i>
                            @else
                            <i class="ri-close-circle-fill text-danger"></i>
                            @endif
                        </td>
                        @can('estoque_view')
                        <td data-label="Estoque">
                            <label style="width: 200px">
                                @if(__countLocalAtivo() == 1)
                                {{ $item->estoqueAtual() }}
                                @else
                                @foreach($item->estoqueLocais as $e)
                                @if($e->local)
                                {{ $e->local->descricao }}:
                                <strong class="text-success">
                                    @if(!$item->unidadeDecimal())
                                    {{ number_format($e->quantidade, 0, '.', '') }}
                                    @else
                                    {{ number_format($e->quantidade, 3, '.', '') }}
                                    @endif
                                </strong>
                                @endif
                                @if(!$loop->last) | @endif
                                @endforeach
                                @endif
                            </label>
                        </td>
                        @endcan
                        <td data-label="Status">
                            @if($item->status)
                            <i class="ri-checkbox-circle-fill text-success"></i>
                            @else
                            <i class="ri-close-circle-fill text-danger"></i>
                            @endif
                        </td>
                        <td data-label="Variação">
                            @if(sizeof($item->variacoes) > 0)
                            <i class="ri-checkbox-circle-fill text-success"></i>
                            @else
                            <i class="ri-close-circle-fill text-danger"></i>
                            @endif
                        </td>
                        <td data-label="Combo">
                            @if($item->combo)
                            <i class="ri-checkbox-circle-fill text-success"></i>
                            @else
                            <i class="ri-close-circle-fill text-danger"></i>
                            @endif
                        </td>
                        @if(__isActivePlan(Auth::user()->empresa, 'Cardapio'))
                        <td data-label="Cardápio">
                            @if($item->cardapio)
                            <i class="ri-checkbox-circle-fill text-success"></i>
                            @else
                            <i class="ri-close-circle-fill text-danger"></i>
                            @endif
                        </td>
                        @endif
                        @if(__isActivePlan(Auth::user()->empresa, 'Delivery'))
                        <td data-label="Delivery">
                            @if($item->delivery)
                            <i class="ri-checkbox-circle-fill text-success"></i>
                            @else
                            <i class="ri-close-circle-fill text-danger"></i>
                            @endif
                        </td>
                        @endif
                        @if(__isActivePlan(Auth::user()->empresa, 'Ecommerce'))
                        <td data-label="Ecommerce">
                            @if($item->ecommerce)
                            <i class="ri-checkbox-circle-fill text-success"></i>
                            @else
                            <i class="ri-close-circle-fill text-danger"></i>
                            @endif
                        </td>
                        @endif
                        @if(__isActivePlan(Auth::user()->empresa, 'Reservas'))
                        <td data-label="Reserva">
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
                        <td colspan="23" class="text-center">Nada encontrado</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
    <button type="button" id="scrollToggle2" class="scroll-btn-jidox hidden">
        <i class="ri-arrow-right-circle-line"></i>
    </button>
</div>