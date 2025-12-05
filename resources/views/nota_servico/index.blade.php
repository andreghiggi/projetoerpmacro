@extends('layouts.app', ['title' => 'Notas de Serviço'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        @can('nfse_create')
                        <a href="{{ route('nota-servico.create') }}" class="btn btn-success">
                            <i class="ri-add-circle-fill"></i>
                            Nova NFSe
                        </a>
                        @endcan
                    </div>
                    <div class="col-md-8"></div>

                    
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::text('tomador', 'Tomador')
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
                            'processando' => 'Processando',
                            '' => 'Todos'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-lg-3 col-12">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('nota-servico.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tomador</th>
                                    <th>CPF/CNPJ</th>
                                    <th>Número</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                    <th>Ambiente</th>
                                    <th>Data</th>
                                    <th>Chave</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Tomador">{{ $item->razao_social }}</td>
                                    <td data-label="CPF/CNPJ">{{ $item->documento }}</td>
                                    <td data-label="Número">{{ $item->numero_nfse ? $item->numero_nfse : '' }}</td>
                                    <td data-label="Valor">{{ __moeda($item->valor_total) }}</td>
                                    <td data-label="Estado">
                                        @if($item->estado == 'aprovado')
                                        <span class="btn btn-success text-white btn-sm">Aprovado</span>
                                        @elseif($item->estado == 'cancelado')
                                        <span class="btn btn-danger text-white btn-sm">Cancelado</span>
                                        @elseif($item->estado == 'rejeitado')
                                        <span class="btn btn-warning text-white btn-sm">Rejeitado</span>
                                        @else
                                        <span class="btn btn-info text-white btn-sm">Novo</span>
                                        @endif
                                    </td>
                                    <td data-label="Ambiente">{{ $item->ambiente == 2 ? 'Produção' : 'Produção' }}</td>
                                    <td data-label="Data">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Chave">{{ $item->chave }}</td>
                                    <td>
                                        <form action="{{ route('nota-servico.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 320px">
                                            @method('delete')
                                            @csrf

                                            @if($item->estado == 'aprovado')
                                            <a class="btn btn-dark btn-sm" title="Imprimir NFSe" target="_blank" href="{{ route('nota-servico.imprimir', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                            <button title="Cancelar NFSe" type="button" class="btn btn-danger btn-sm" onclick="cancelar('{{$item->id}}', '{{$item->numero}}')">
                                                <i class="ri-close-circle-line"></i>
                                            </button>
                                            @else
                                            <a title="Visualizar PDF Temorário" target="_blank" class="btn btn-dark btn-sm" href="{{ route('nota-servico.preview', [$item->id]) }}">
                                                <i class="ri-file-ppt-line"></i>
                                            </a>
                                            @endif

                                            @if($item->estado == 'novo' || $item->estado == 'rejeitado')
                                            @can('nfse_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('nota-servico.edit', $item->id) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @endcan
                                            @can('nfse_delete')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan
                                            <button title="Transmitir NFSe" type="button" class="btn btn-success btn-sm" onclick="transmitir('{{$item->id}}')">
                                                <i class="ri-send-plane-fill"></i>
                                            </button>
                                            @endif

                                            <button title="Consultar NFSe" type="button" class="btn btn-light btn-sm" onclick="consultar('{{$item->id}}', '{{$item->numero}}')">
                                                <i class="ri-file-search-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                    {!! $data->appends(request()->all())->links() !!}
                </div>
                <h5 class="mt-2">Soma: <strong class="text-success">R$ {{ __moeda($data->sum('valor_total')) }}</strong></h5>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-cancelar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cancelar NFSe <strong class="ref-numero"></strong></h5>
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


@endsection

@section('js')

<script type="text/javascript" src="/js/nfse_transmitir.js"></script>
@endsection
