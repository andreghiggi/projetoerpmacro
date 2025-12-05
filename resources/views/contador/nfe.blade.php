@extends('layouts.app', ['title' => 'NFe'])
@section('content')

<input type="hidden" id="nao_imprimir" value="1">
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>NFe</h4>

                <div class="col-md-2">
                    <a href="{{ route('contador-empresa-nfe.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Nova Venda
                    </a>
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
                            {!!Form::select('tpNF', 'Tipo',
                            ['' => 'Todos',
                            '1' => 'Saída',
                            '0' => 'Entrada'])
                            ->attrs(['class' => 'form-select'])
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

                        <div class="col-lg-3 col-12">
                            <br>

                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('contador-empresa.nfe') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cliente/Fornecedor</th>
                                    <th>CPF/CNPJ</th>
                                    <th>#</th>
                                    <th>Número</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                    <th>Ambiente</th>
                                    <th>Data</th>
                                    <th>Local de emissão</th>
                                    <th>Tipo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    @if($item->cliente)
                                    <td>{{ $item->cliente ? $item->cliente->razao_social : "--" }}</td>
                                    <td>{{ $item->cliente ? $item->cliente->cpf_cnpj : "--" }}</td>
                                    @else
                                    <td>{{ $item->fornecedor ? $item->fornecedor->razao_social : "--" }}</td>
                                    <td>{{ $item->fornecedor ? $item->fornecedor->cpf_cnpj : "--" }}</td>
                                    @endif
                                    <td>{{ $item->numero_sequencial }}</td>

                                    <td>{{ $item->numero ? $item->numero : '' }}</td>
                                    <td>{{ __moeda($item->total) }}</td>
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
                                    <td>{{ __data_pt($item->created_at) }}</td>
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
                                    <td>
                                        <form action="{{ route('contador-empresa-nfe.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 220px">
                                            @method('delete')
                                            @csrf
                                            @if($item->estado == 'aprovado')
                                            <a class="btn btn-primary btn-sm" title="Download XML" href="{{ route('contador-empresa-nfe.download', [$item->id]) }}">
                                                <i class="ri-file-download-fill"></i>
                                            </a>
                                            <a target="_blank" class="btn btn-dark btn-sm" title="Danfe" href="{{ route('contador-empresa-nfe.danfe', [$item->id]) }}">
                                                <i class="ri-printer-fill"></i>
                                            </a>

                                            <button title="Cancelar NFe" type="button" class="btn btn-danger btn-sm" onclick="cancelar('{{$item->id}}', '{{$item->numero}}')">
                                                <i class="ri-close-circle-line"></i>
                                            </button>
                                            <button title="Corrigir NFe" type="button" class="btn btn-warning btn-sm" onclick="corrigir('{{$item->id}}', '{{$item->numero}}')">
                                                <i class="ri-file-warning-line"></i>
                                            </button>

                                            @else

                                            @if($item->estado != 'cancelado')

                                            <a class="btn btn-warning btn-sm" href="{{ route('contador-empresa-nfe.edit', $item->id) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>

                                            <button title="Transmitir NFe" type="button" class="btn btn-success btn-sm" onclick="transmitir('{{$item->id}}')">
                                                <i class="ri-send-plane-fill"></i>
                                            </button>

                                            <a target="_blank" title="XML temporário" class="btn btn-light btn-sm" href="{{ route('contador-empresa-nfe.xml-temp', $item->id) }}">
                                                <i class="ri-file-line"></i>
                                            </a>

                                            <button type="button" class="btn btn-danger btn-sm btn-delete"><i class="ri-delete-bin-line"></i></button>
                                            @endif
                                            @endif

                                            @if($item->estado == 'cancelado')
                                            <a class="btn btn-danger btn-sm" target="_blank" href="{{ route('contador-empresa-nfe.imprimir-cancela', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                            @endif

                                            @if($item->sequencia_cce > 0)
                                            <a class="btn btn-warning btn-sm" target="_blank" href="{{ route('contador-empresa-nfe.imprimir-correcao', [$item->id]) }}">
                                                <i class="ri-printer-fill"></i>
                                            </a>
                                            @endif
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
                    <br>
                    {!! $data->appends(request()->all())->links() !!}
                </div>
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <h5 class="mt-2">Soma: <strong class="text-success">R$ {{ __moeda($data->sum('total')) }}</strong></h5>
                    </div>
                    @if($contXml > 0)
                    <div class="col-lg-6 col-12">

                        <h5 class="mt-2 float-end">Total de arquivos XML: <strong class="text-primary">{{ $contXml }}</strong></h5>
                        <br><br>
                        <a class="btn btn-dark float-end" href="{{ route('contador-empresa-nfe-zip', ['start_date='.request()->start_date, 'end_date='.request()->end_date, 'tpNF='.request()->tpNF, 'estado='.request()->estado]) }}">
                            Download arquivo ZIP
                        </a>
                    </div>
                    @endif
                </div>
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

    $('#btn-consulta-sefaz').click(() => {

        $.post(path_url + 'api/nfe_painel/consulta-status-sefaz', {
            empresa_id: $('#empresa_id').val(),
            usuario_id: $('#usuario_id').val(),
        })
        .done((res) => {
            let msg = "cStat: " + res.cStat
            msg += "\nMotivo: " + res.xMotivo
            msg += "\nAmbiente: " + (res.tpAmb == 2 ? "Homologação" : "Produção")
            msg += "\nverAplic: " + res.verAplic

            swal("Sucesso", msg, "success")
        })
        .fail((err) => {
            try {
                swal("Erro", err.responseText, "error")
            } catch {
                swal("Erro", "Algo deu errado", "error")
            }
        })
    })

</script>
<script type="text/javascript" src="/js/nfe_transmitir.js"></script>
@endsection
