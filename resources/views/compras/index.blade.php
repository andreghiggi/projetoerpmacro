@extends('layouts.app', ['title' => 'Compras'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('compras_create')
                    <a href="{{ route('compras.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Compra
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-4">
                            {!!Form::select('fornecedor_id', 'Fornecedor', ['' => 'Selecione'] + $fornecedores->pluck('razao_social', 'id')->all())
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
                            ['novo' => 'Novas',
                            'rejeitado' => 'Rejeitadas',
                            'cancelado' => 'Canceladas',
                            'aprovado' => 'Aprovadas',
                            '' => 'Todos'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        @if(__countLocalAtivo() > 1)
                        <div class="col-md-2">
                            {!!Form::select('local_id', 'Local', ['' => 'Selecione'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>
                        @endif
                        <div class="col-md-4 col-xl-2 col-12">
                            <br>

                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('compras.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                @if($contigencia != null)
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="text-danger">Contigência ativada</h4>
                                <p class="text-danger">Tipo: <strong>{{$contigencia->tipo}}</strong></p>
                                <p class="text-danger">Data de ínicio: <strong>{{ __data_pt($contigencia->created_at) }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ações</th>
                                    <th>#</th>
                                    <th>Fornecedor</th>
                                    @if(__countLocalAtivo() > 1)
                                    <th>Local</th>
                                    @endif
                                    <th>CPF/CNPJ</th>
                                    <th>Número</th>
                                    <th>XML Importado</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                    <th>Ambiente</th>
                                    <th>Data</th>
                                    <th>Data de emissão</th>
                                    <th>Local de emissão</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>
                                        <form action="{{ route('nfe.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 300px;">
                                            @method('delete')
                                            @csrf
                                            @if($item->estado == 'cancelado')
                                            <a class="btn btn-danger btn-sm" target="_blank" href="{{ route('nfe.imprimir-cancela', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                            @endif
                                            @if($item->estado == 'aprovado')
                                            <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('nfe.imprimir', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>

                                            @can('nfe_transmitir')
                                            <button title="Cancelar NFe" type="button" class="btn btn-danger btn-sm" onclick="cancelar('{{$item->id}}', '{{$item->numero}}')">
                                                <i class="ri-close-circle-line"></i>
                                            </button>
                                            <button title="Corrigir NFe" type="button" class="btn btn-warning btn-sm" onclick="corrigir('{{$item->id}}', '{{$item->numero}}')">
                                                <i class="ri-file-warning-line"></i>
                                            </button>
                                            @endcan

                                            @endif

                                            @if($item->estado == 'aprovado' || $item->estado == 'rejeitado')
                                            <button type="button" class="btn btn-dark btn-sm" onclick="info('{{$item->motivo_rejeicao}}', '{{$item->chave}}', '{{$item->estado}}', '{{$item->recibo}}')">
                                                <i class="ri-file-line"></i>
                                            </button>
                                            @endif
                                            @if($item->estado == 'novo' || $item->estado == 'rejeitado')

                                            @if($item->chave_importada == '')
                                            @can('compras_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('nfe.edit', $item->id) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @endcan
                                            @endif

                                            <a target="_blank" title="XML temporário" class="btn btn-light btn-sm" href="{{ route('nfe.xml-temp', $item->id) }}">
                                                <i class="ri-file-line"></i>
                                            </a>
                                            @can('compras_delete')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete"><i class="ri-delete-bin-line"></i></button>
                                            @endcan

                                            @can('nfe_transmitir')
                                            <button title="Transmitir NFe" type="button" class="btn btn-success btn-sm" onclick="transmitir('{{$item->id}}')">
                                                <i class="ri-send-plane-fill"></i>
                                            </button>
                                            @endcan
                                            
                                            @endif

                                            @if($item->estado == 'aprovado' || $item->estado == 'cancelado')
                                            <button title="Consultar NFe" type="button" class="btn btn-light btn-sm" onclick="consultar('{{$item->id}}', '{{$item->numero}}')">
                                                <i class="ri-file-search-line"></i>
                                            </button>
                                            @endif

                                            @if($item->isItemValidade())
                                                <a href="{{ route('compras.info-validade', $item->id) }}" title="Editar Validade" type="button" class="btn btn-info btn-sm"><i class="ri-pencil-line"></i></a>
                                            @endif

                                            <!-- <a class="btn btn-info btn-sm" title="Imprimir Pedido" target="_blank" href="{{ route('nfe.imprimirVenda', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a> -->
                                            <a class="btn btn-info btn-sm" title="Imprimir Pedido" onclick="printPedido('{{ $item->id }}')">
                                                <i class="ri-printer-line"></i>
                                            </a>

                                            <a class="btn btn-light btn-sm" title="Gerar etiqueta" target="_blank" href="{{ route('compras.etiqueta', [$item->id]) }}">
                                                <i class="ri-barcode-box-line"></i>
                                            </a>
                                        </form>
                                    </td>
                                    <td>{{ $item->numero_sequencial }}</td>
                                    <td>{{ $item->fornecedor ? $item->fornecedor->razao_social : "--" }}</td>
                                    @if(__countLocalAtivo() > 1)
                                    <td class="text-danger">{{ $item->localizacao->descricao }}</td>
                                    @endif
                                    <td>{{ $item->fornecedor ? $item->fornecedor->cpf_cnpj : "--" }}</td>
                                    <td>{{ $item->numero ? $item->numero : '' }}</td>
                                    <td>
                                        @if($item->chave_importada)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->total, 2, ',', '.') }}</td>
                                    <td width="150">
                                        @if($item->estado == 'aprovado')
                                        <span class="btn btn-success text-white btn-sm w-100">Aprovado</span>
                                        @elseif($item->estado == 'cancelado')
                                        <span class="btn btn-danger text-white btn-sm w-100">Cancelado</span>
                                        @elseif($item->estado == 'rejeitado')
                                        <span class="btn btn-warning text-white btn-sm w-100">Rejeitado</span>
                                        @else
                                        <span class="btn btn-info text-white btn-sm w-100">Novo</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->ambiente == 2 ? 'Homologação' : 'Produção' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                                    <td><label style="width: 120px">{{ $item->data_emissao ? __data_pt($item->data_emissao, 1) : '--' }}</label></td>

                                    <td>
                                        @if($item->api)
                                        <span class="text-success">API</span>
                                        @else
                                        <span class="text-primary">Painel</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->tpNF)
                                        <span class="text-success">Saída</span>
                                        @else
                                        <span class="text-primary">Entrada</span>
                                        @endif
                                    </td>
                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <br>
                    {!! $data->appends(request()->all())->links() !!}
                </div>
                <h5 class="mt-2">Soma: <strong class="text-success">R$ {{ __moeda($data->sum('total')) }}</strong></h5>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-cancelar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cancelar NFe <strong class="ref-numero"></strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12">
                        {!!Form::text('motivo-cancela', 'Motivo')
                        ->required()

                        !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                <button type="button" id="btn-cancelar" class="btn btn-danger">Cancelar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-corrigir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Corrigir NFe <strong class="ref-numero"></strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12">
                        {!!Form::text('motivo-corrigir', 'Motivo')
                        ->required()

                        !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                <button type="button" id="btn-corrigir" class="btn btn-warning">Corrigir</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    function info(motivo_rejeicao, chave, estado, recibo) {

        if (estado == 'rejeitado') {
            let text = "Motivo: " + motivo_rejeicao + "\n"
            text += "Chave: " + chave + "\n"
            swal("", text, "warning")
        } else {
            let text = "Chave: " + chave + "\n"
            text += "Recibo: " + recibo + "\n"
            swal("", text, "success")
        }
    }

    function printPedido(id){
        var disp_setting="toolbar=yes,location=no,";
        disp_setting+="directories=yes,menubar=yes,";
        disp_setting+="scrollbars=yes,width=850, height=600, left=100, top=25";

        var docprint=window.open(path_url+"nfe/imprimirVenda/"+id, "",disp_setting);

        docprint.focus();
    }
</script>
<script type="text/javascript" src="/js/nfe_transmitir.js"></script>
@endsection
