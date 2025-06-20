@extends('layouts.app', ['title' => 'NFe'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>NFe</h4>
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
                            [
                            '' => 'Todos',
                            '1' => 'Saída',
                            '0' => 'Entrada',
                            ])
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
                                        <a class="btn btn-primary btn-sm" title="Download XML" href="{{ route('contador-empresa-nfe.download', [$item->id]) }}">
                                            <i class="ri-file-download-fill"></i>
                                        </a>

                                        @if($item->estado == 'aprovado')
                                        <a target="_blank" class="btn btn-dark btn-sm" title="Danfe" href="{{ route('contador-empresa-nfe.danfe', [$item->id]) }}">
                                            <i class="ri-printer-fill"></i>
                                        </a>
                                        @endif

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nada encontrado</td>
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
