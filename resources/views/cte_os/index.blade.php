@extends('layouts.app', ['title' => 'CTe Os'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-2">
                    @can('cte_os_create')
                    <a href="{{ route('cte-os.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova CTe Os
                    </a>
                    @endcan
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">

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
                            ['novo' => 'Nova',
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
                        <div class="col-md-2">
                            <br>

                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('cte-os.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Emitente</th>
                                    <th>Tomador</th>
                                    @if(__countLocalAtivo() > 1)
                                    <th>Local</th>
                                    @endif
                                    <th>Valor serviço</th>
                                    <th>Valor a receber</th>
                                    <th>Estado</th>
                                    <th>Data cadastro</th>
                                    <th>Tomador</th>
                                    <th>Nº</th>
                                    <th>Local de emissão</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Emitente">{{ $item->emitente ? $item->emitente->razao_social : "--" }}</td>
                                    <td data-label="Tomador">{{ $item->tomador_cli ? $item->tomador_cli->razao_social : "--" }}</td>
                                    @if(__countLocalAtivo() > 1)
                                    <td data-label="Local" class="text-danger">{{ $item->localizacao ? $item->localizacao->descricao : '' }}</td>
                                    @endif
                                    <td data-label="Valor serviço">{{ __moeda($item->valor_transporte) }}</td>
                                    <td data-label="Valor a receber">{{ __moeda($item->valor_receber) }}</td>
                                    <td data-label="Estado">{!! $item->estadoEmissao() !!}</td>
                                    <td data-label="Data cadastro">{{ __data_pt($item->created_at, 1) }}</td>
                                    <td data-label="Tomador (Tipo)">{{ $item->getTomador() }}</td>
                                    <td data-label="Nº">{{ $item->numero ? $item->numero : '--' }}</td>
                                    <td data-label="Local de emissão">
                                        @if($item->api)
                                        <span class="text-success">API</span>
                                        @else
                                        <span class="text-primary">Painel</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form style="width: 250px;" action="{{ route('cte-os.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf
                                            @if($item->estado_emissao == 'cancelado')
                                            <a class="btn btn-danger btn-sm" target="_blank" href="{{ route('cte-os.imprimir-cancela', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                            @endif
                                            @if($item->estado_emissao == 'aprovado')
                                            <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('cte-os.imprimir', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>

                                            <a class="btn btn-light btn-sm" target="_blank" href="{{ route('cte-os.download', [$item->id]) }}">
                                                <i class="ri-download-2-fill"></i>
                                            </a>

                                            <button title="Cancelar CTeOs" type="button" class="btn btn-danger btn-sm" onclick="cancelar('{{$item->id}}', '{{$item->numero}}')">
                                                <i class="ri-close-circle-line"></i>
                                            </button>
                                            @endif

                                            @if($item->estado_emissao == 'aprovado' || $item->estado_emissao == 'rejeitado')
                                            <button type="button" title="Consultar Chave" class="btn btn-dark btn-sm" onclick="info('{{$item->motivo_rejeicao}}', '{{$item->chave}}', '{{$item->estado}}', '{{$item->recibo}}')">
                                                <i class="ri-file-line"></i>
                                            </button>
                                            @endif
                                            @if($item->estado_emissao == 'novo' || $item->estado_emissao == 'rejeitado')

                                            @can('cte_os_edit')
                                            <a class="btn btn-warning btn-sm" href="{{ route('cte-os.edit', $item->id) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @endcan

                                            <a target="_blank" title="XML temporário" class="btn btn-light btn-sm" href="{{ route('cte-os.xml-temp', $item->id) }}">
                                                <i class="ri-file-line"></i>
                                            </a>

                                            @can('cte_os_delete')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete"><i class="ri-delete-bin-line"></i></button>
                                            @endcan

                                            <button title="Transmitir CTe" type="button" class="btn btn-success btn-sm" onclick="transmitir('{{$item->id}}')">
                                                <i class="ri-send-plane-fill"></i>
                                            </button>
                                            @endif

                                            @if($item->estado_emissao == 'aprovado' || $item->estado_emissao == 'cancelado')
                                            <button title="Consultar CTe" type="button" class="btn btn-light btn-sm" onclick="consultar('{{$item->id}}', '{{$item->numero}}')">
                                                <i class="ri-file-search-line"></i>
                                            </button>
                                            @endif
                                            <a title="Alterar estado fiscal" class="btn btn-danger btn-sm" href="{{ route('cte-os.alterar-estado', $item->id) }}">
                                                <i class="ri-arrow-up-down-line"></i>
                                            </a>
                                        </form>
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
                    {!! $data->appends(request()->all())->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-cancelar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cancelar CTe <strong class="ref-numero"></strong></h5>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Corrigir CTe <strong class="ref-numero"></strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        {!! Form::select('grupo', 'Grupo')
                        ->attrs(['class' => 'form-select'])->required()
                        ->options(App\Models\Cte::gruposCte()) !!}
                    </div>
                    <div class="col-md-2">
                        {!! Form::text('campo', 'Campo')->required() !!}
                    </div>
                    <div class="col-md-8">
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

</script>
<script type="text/javascript" src="/js/cte_os_transmitir.js"></script>
@endsection
