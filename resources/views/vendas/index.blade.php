@extends('layouts.app', ['title' => 'Vendas'])
@section('css')
<style type="text/css">
    .btn{
        margin-top: 3px;
    }

    input[type="file"] {
        display: none;
    }

    .file-certificado label {
        padding: 8px 8px;
        width: 100%;
        background-color: #8833FF;
        color: #FFF;
        text-transform: uppercase;
        text-align: center;
        display: block;
        margin-top: 20px;
        cursor: pointer;
        border-radius: 5px;
    }
</style>
@endsection
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">

                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3 g-1">
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
                        @if(__isPlanoFiscal())
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

                        @endif

                        @if(__countLocalAtivo() > 1)
                        <div class="col-md-2">
                            {!!Form::select('local_id', 'Local', ['' => 'Selecione'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>
                        @endif

                        <div class="col-lg-4 col-12">
                            <br>

                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('vendas.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>

                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ações</th>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>CPF/CNPJ</th>
                                    @if(__countLocalAtivo() > 1)
                                    <th>Local</th>
                                    @endif
                                    <th>Usuário</th>
                                    <th>Número</th>
                                    <th>Número Série</th>
                                    <th>Valor</th>
                                    @if(__isPlanoFiscal())
                                    <th>Estado</th>
                                    <th>Ambiente</th>
                                    @endif
                                    <th>Data de cadastro</th>
                                    <th>Data de emissão</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>
                                        @if($item->tipo == 'Nfe')
                                        <form action="{{ route('nfe.destroy', $item->id) }}" method="post" id="form-nfe-{{$item->id}}" style="width: 160px">
                                            @method('delete')
                                            @csrf

                                            @if($item->estado == 'novo' || $item->estado == 'rejeitado')
                                            @can('nfe_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('nfe.edit', $item->id) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @endcan

                                            @can('nfe_delete')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete"><i class="ri-delete-bin-line"></i></button>
                                            @endcan
                                            @endif

                                            @if($envioWppLink)
                                            <button title="Enviar Mensagem" onclick="enviarWpp('{{$item->id}}', 'nfe')" type="button" class="btn btn-success btn-sm">
                                                <i class="ri-whatsapp-fill"></i>
                                            </button>
                                            @endif
                                        </form>
                                        @else
                                        <form action="{{ route('frontbox.destroy', $item->id) }}" method="post" id="form-nfce-{{$item->id}}" style="width: 160px">
                                            @method('delete')
                                            @csrf

                                            @if($item->estado == 'novo' || $item->estado == 'rejeitado')
                                            @can('nfe_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('frontbox.edit', $item->id) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @endcan

                                            @can('nfe_delete')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete"><i class="ri-delete-bin-line"></i></button>
                                            @endcan
                                            @endif

                                            @if($envioWppLink)
                                            <button title="Enviar Mensagem" onclick="enviarWpp('{{$item->id}}', 'nfce')" type="button" class="btn btn-success btn-sm">
                                                <i class="ri-whatsapp-fill"></i>
                                            </button>
                                            @endif
                                        </form>
                                        @endif
                                    </td>

                                    <td data-label="#">
                                        <label style="width: 120px">
                                            {{ $item->tipo == 'Nfe' ? 'PEDIDO' : 'PDV' }}
                                            <strong class="text-success">#{{ $item->numero_sequencial }}</strong>
                                        </label>
                                    </td>

                                    <td data-label="Cliente">
                                        <label style="width: 350px">{{ $item->razao_social }}</label>
                                    </td>

                                    <td data-label="CPF/CNPJ">{{ $item->cpf_cnpj }}</td>

                                    @if(__countLocalAtivo() > 1)
                                    <td data-label="Local" class="text-danger">{{ $item->descricao }}</td>
                                    @endif

                                    <td data-label="Usuário">{{ $item->user_name }}</td>
                                    <td data-label="Número">{{ $item->estado == 'aprovado' || $item->estado == 'cancelado' ? $item->numero : '--' }}</td>

                                    <td data-label="Número Série">
                                        <label style="width: 100px">{{ $item->numero_serie ? $item->numero_serie : '' }}</label>
                                    </td>

                                    <td data-label="Valor">{{ __moeda($item->total) }}</td>

                                    @if(__isPlanoFiscal())
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
                                    @endif

                                    <td data-label="Data de cadastro">
                                        <label style="width: 120px">{{ __data_pt($item->created_at) }}</label>
                                    </td>

                                    <td data-label="Data de emissão">
                                        <label style="width: 120px">{{ $item->data_emissao ? __data_pt($item->data_emissao, 1) : '--' }}</label>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="14" class="text-center">Nada encontrado</td>
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
@section('js')
<script type="text/javascript" src="/js/enviar_fatura_wpp.js"></script>

@endsection
@endsection


