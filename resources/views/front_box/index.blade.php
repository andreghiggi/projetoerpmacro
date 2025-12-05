@extends('layouts.app', ['title' => 'Lista de Vendas PDV'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('pdv_create')
                    <a href="{{ route('frontbox.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        PDV
                    </a>
                    @endcan
                </div>
                <hr>
                {!! Form::open()->fill(request()->all())->get() !!}
                <div class="row">
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
                    
                    @if($adm)
                    <div class="col-md-2">
                        {!!Form::select('user_id', 'Usuário', ['' => 'Selecione'] + $usuarios->pluck('name', 'id')->all())
                        ->attrs(['class' => 'select2'])
                        !!}
                    </div>
                    @endif
                    <div class="col-md-4 text-left">
                        <br>
                        <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                        <a id="clear-filter" class="btn btn-danger" href="{{ route('frontbox.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                    </div>
                </div>
                {!! Form::close() !!}

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
                <div class="col-lg-12 mt-4">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ações</th>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>CPF/CNPJ</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                    <th>Ambiente</th>
                                    <th>Número NFCe</th>
                                    <th>Data</th>
                                    <th>Lista de preço</th>
                                    <th>Usuário</th>
                                    <th>Vendedor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td class="text-start d-none d-md-table-cell">
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static">
                                                Ações
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end shadow">

                                                <form action="{{ route('frontbox.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                                    @csrf
                                                    @method('delete')

                                                    <li>
                                                        <button type="button" class="dropdown-item" onclick="imprimir('{{$item->id}}')">
                                                            <i class="ri-printer-line me-1 text-primary"></i> Imprimir Não Fiscal
                                                        </button>
                                                    </li>

                                                    <li>
                                                        <button type="button" class="dropdown-item" onclick="imprimirA4('{{$item->id}}')">
                                                            <i class="ri-printer-fill me-1"></i> Imprimir A4
                                                        </button>
                                                    </li>

                                                    @can('pdv_delete')
                                                    @if($item->estado == 'novo' || $item->estado == 'rejeitado')
                                                    <li>
                                                        <button type="button" class="dropdown-item text-danger btn-delete" data-id="{{ $item->id }}">
                                                            <i class="ri-delete-bin-line me-1"></i> Excluir
                                                        </button>
                                                    </li>
                                                    @endif
                                                    @endcan

                                                    @if($item->estado == 'novo' || $item->estado == 'rejeitado')
                                                    <li>
                                                        <button type="button" class="dropdown-item text-success" onclick="transmitir('{{$item->id}}')">
                                                            <i class="ri-send-plane-fill me-1"></i> Transmitir NFC-e
                                                        </button>
                                                    </li>

                                                    @can('pdv_edit')
                                                    <li>
                                                        <a class="dropdown-item text-warning" href="{{ route('frontbox.edit', $item->id) }}">
                                                            <i class="ri-pencil-line me-1"></i> Editar Venda
                                                        </a>
                                                    </li>
                                                    @endcan
                                                    @endif

                                                    @if($item->estado != 'aprovado')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('frontbox.show', $item->id) }}">
                                                            <i class="ri-eye-line me-1"></i> Detalhes
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item text-dark" target="_blank" href="{{ route('nfce.xml-temp', $item->id) }}">
                                                            <i class="ri-file-line me-1"></i> XML Temporário
                                                        </a>
                                                    </li>
                                                    @endif

                                                    @if($item->estado == 'aprovado')
                                                    <li>
                                                        <a class="dropdown-item text-info" target="_blank" href="{{ route('nfce.imprimir', [$item->id]) }}">
                                                            <i class="ri-printer-line me-1"></i> Imprimir NFC-e
                                                        </a>
                                                    </li>
                                                    @endif

                                                    @if($item->cliente && sizeof($item->fatura) > 0)
                                                    <li>
                                                        <a class="dropdown-item" target="_blank" href="{{ route('frontbox.imprimir-carne', [$item->id]) }}">
                                                            <i class="ri-currency-line me-1"></i> Imprimir Carnê
                                                        </a>
                                                    </li>
                                                    @endif

                                                    @if($envioWppLink)
                                                    <li>
                                                        <button type="button" class="dropdown-item text-success" onclick="enviarWpp('{{$item->id}}', 'nfce')">
                                                            <i class="ri-whatsapp-fill me-1"></i> Enviar WhatsApp
                                                        </button>
                                                    </li>
                                                    @endif

                                                </form>

                                            </ul>
                                        </div>
                                    </td>

                                    <td class="d-md-none">
                                        <form action="{{ route('frontbox.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 380px">
                                            @method('delete')
                                            @csrf

                                            <a title="Imprimir não fiscal" onclick="imprimir('{{$item->id}}')" class="btn btn-primary btn-sm">
                                                <i class="ri-printer-line"></i>
                                            </a>

                                            <a title="Imprimir A4" onclick="imprimirA4('{{$item->id}}')" class="btn btn-dark btn-sm">
                                                <i class="ri-printer-fill"></i>
                                            </a>

                                            @can('pdv_delete')
                                            @if($item->estado == 'novo' || $item->estado == 'rejeitado')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endif
                                            @endcan

                                            @if($item->estado == 'novo' || $item->estado == 'rejeitado')
                                            <button title="Transmitir NFCe" type="button" class="btn btn-success btn-sm" onclick="transmitir('{{$item->id}}')">
                                                <i class="ri-send-plane-fill"></i>
                                            </button>

                                            @can('pdv_edit')
                                            <a class="btn btn-warning btn-sm" title="Editar venda" href="{{ route('frontbox.edit', $item->id) }}">
                                                <i class="ri-pencil-line"></i>
                                            </a>
                                            @endcan
                                            @endif

                                            @if($item->estado != 'aprovado')
                                            <a class="btn btn-ligth btn-sm" title="Detalhes" href="{{ route('frontbox.show', $item->id) }}">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a target="_blank" title="XML temporário" class="btn btn-dark btn-sm" href="{{ route('nfce.xml-temp', $item->id) }}">
                                                <i class="ri-file-line"></i>
                                            </a>
                                            @endif

                                            @if($item->estado == 'aprovado')
                                            <a class="btn btn-info btn-sm" title="Imprimir NFCe" target="_blank" href="{{ route('nfce.imprimir', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                            @endif

                                            @if($item->cliente && sizeof($item->fatura) > 0)
                                            <a target="_blank" title="Imprimir carnê" href="{{ route('frontbox.imprimir-carne', [$item->id]) }}" class="btn btn-light btn-sm">
                                                <i class="ri-currency-line"></i>
                                            </a>
                                            @endif

                                            @if($envioWppLink)
                                            <button title="Enviar Mensagem" onclick="enviarWpp('{{$item->id}}', 'nfce')" type="button" class="btn btn-success btn-sm">
                                                <i class="ri-whatsapp-fill"></i>
                                            </button>
                                            @endif
                                        </form>
                                    </td>

                                    <td data-label="#">{{ $item->numero_sequencial }}</td>

                                    <td data-label="Cliente">
                                        <label style="width: 350px">
                                            {{ $item->cliente ? $item->cliente->razao_social : ($item->cliente_nome != "" ? $item->cliente_nome : "--") }}
                                        </label>
                                    </td>

                                    <td data-label="CPF/CNPJ">
                                        <label style="width: 150px">
                                            {{ $item->cliente ? $item->cliente->cpf_cnpj : ($item->cliente_cpf_cnpj != "" ? $item->cliente_cpf_cnpj : "--") }}
                                        </label>
                                    </td>

                                    <td data-label="Valor">{{ __moeda($item->total) }}</td>

                                    <td data-label="Estado">
                                        @if($item->estado == 'aprovado')
                                        <span class="badge p-1 bg-success text-white">APROVADO</span>
                                        @elseif($item->estado == 'cancelado')
                                        <span class="badge p-1 bg-danger text-white">CANCELADO</span>
                                        @elseif($item->estado == 'rejeitado')
                                        <span class="badge p-1 bg-warning text-white">REJEITADO</span>
                                        @else
                                        <span class="badge p-1 bg-info text-white">NOVO</span>
                                        @endif
                                    </td>

                                    <td data-label="Ambiente">{{ $item->ambiente == 2 ? 'Homologação' : 'Produção' }}</td>

                                    <td data-label="Número NFCe">
                                        <label style="width: 100px">{{ $item->estado == 'aprovado' ? $item->numero : '--' }}</label>
                                    </td>

                                    <td data-label="Data">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>

                                    <td data-label="Lista de preço">
                                        <label style="width: 100px">{{ $item->lista ? $item->listaPreco->nome : '--' }}</label>
                                    </td>

                                    <td data-label="Usuário">{{ $item->user ? $item->user->name : '--' }}</td>

                                    <td data-label="Vendedor">{{ $item->vendedor() ? $item->vendedor() : '--' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                    <br>
                    {!! $data->appends(request()->all())->links() !!}
                </div>
                <h5 class="mt-2">VALOR TOTAL DAS VENDAS: <strong class="text-success">R$ {{ __moeda($somaGeral) }}</strong></h5>
            </div>
        </div>
    </div>
</div>
@include('nfe.partials.modal_envio_wpp')

@endsection

@section('js')
<script type="text/javascript" src="/js/nfce_transmitir.js"></script>
<script type="text/javascript">
    function imprimir(id){
        var disp_setting="toolbar=yes,location=no,";
        disp_setting+="directories=yes,menubar=yes,";
        disp_setting+="scrollbars=yes,width=850, height=600, left=100, top=25";

        var docprint=window.open(path_url+"frontbox/imprimir-nao-fiscal/"+id, "",disp_setting);

        docprint.focus();
    }

    function imprimirA4(id){
        var disp_setting="toolbar=yes,location=no,";
        disp_setting+="directories=yes,menubar=yes,";
        disp_setting+="scrollbars=yes,width=850, height=600, left=100, top=25";

        var docprint=window.open(path_url+"frontbox/imprimir-a4/"+id, "",disp_setting);

        docprint.focus();
    }
</script>
<script type="text/javascript" src="/js/enviar_fatura_wpp.js"></script>

@endsection
