@extends('layouts.app', ['title' => 'Lista de Vendas PDV'])
@section('content')
<div class="mt-3">
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
                    <div class="col-md-2 text-left">
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
                                    <td>
                                        <form action="{{ route('frontbox.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 320px">
                                            @method('delete')
                                            @csrf
                                            <a title="Imprimir não fiscal" onclick="imprimir('{{$item->id}}')" class="btn btn-primary btn-sm">
                                                <i class="ri-printer-line"></i>
                                            </a>

                                            <!-- <a class="btn btn-warning btn-sm" href="{{ route('frontbox.edit', $item->id) }}">
                                                <i class="ri-edit-line"></i>
                                            </a> -->
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
                                            <a class="btn btn-warning btn-sm" title="Editar venda" href="{{ route('frontbox.edit', $item->id) }}"><i class="ri-pencil-line"></i></a>
                                            @endcan

                                            @endif

                                            @if($item->estado != 'aprovado')
                                            <a class="btn btn-ligth btn-sm" title="Detalhes" href="{{ route('frontbox.show', $item->id) }}"><i class="ri-eye-line"></i></a>
                                            <a target="_blank" title="XML temporário" class="btn btn-dark btn-sm" href="{{ route('nfce.xml-temp', $item->id) }}">
                                                <i class="ri-file-line"></i>
                                            </a>
                                            @endif

                                            @if($item->estado == 'aprovado')
                                            <a class="btn btn-success btn-sm" title="Imprimir NFCe" target="_blank" href="{{ route('nfce.imprimir', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                            @endif

                                        </form>
                                    </td>
                                    <td>{{ $item->numero_sequencial }}</td>

                                    <td><label style="width: 350px">{{ $item->cliente ? $item->cliente->razao_social : ($item->cliente_nome != "" ? $item->cliente_nome : "--") }}</label></td>
                                    <td><label style="width: 150px">{{ $item->cliente ? $item->cliente->cpf_cnpj : ($item->cliente_cpf_cnpj != "" ? $item->cliente_cpf_cnpj : "--") }}</label></td>
                                    <td>{{ __moeda($item->total) }}</td>
                                    <td width="150">
                                        @if($item->estado == 'aprovado')
                                        <span class="btn btn-success text-white btn-sm w-100">aprovado</span>
                                        @elseif($item->estado == 'cancelado')
                                        <span class="btn btn-danger text-white btn-sm w-100">cancelado</span>
                                        @elseif($item->estado == 'rejeitado')
                                        <span class="btn btn-warning text-white btn-sm w-100">rejeitado</span>
                                        @else
                                        <span class="btn btn-info text-white btn-sm w-100">novo</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->ambiente == 2 ? 'Homologação' : 'Produção' }}</td>
                                    <td>{{ $item->estado == 'aprovado' ? $item->numero : '--' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                                    <td style="width: 200px;">{{ $item->lista ? $item->listaPreco->nome : '--' }}</td>
                                    <td>{{ $item->user ? $item->user->name : '--' }}</td>
                                    <td>{{ $item->vendedor() ? $item->vendedor() : '--' }}</td>
                                    
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
</script>
@endsection
