@extends('layouts.app', ['title' => 'MDFe'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>MDFe</h4>
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


                        <div class="col-lg-3 col-12">
                            <br>

                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('contador-empresa.mdfe') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Data Início da Viagem</th>
                                    <th>Data Criação</th>
                                    <th>CNPJ Contratante</th>
                                    <th>Estado Fiscal</th>
                                    <th>Chave</th>
                                    <th>Número</th>
                                    <th>Veículo Tração</th>
                                    <th>Quantidade Carga</th>
                                    <th>Valor Cargar</th>
                                    <th>Local de emissão</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>{{ __data_pt($item->data_inicio_viagem, 0) }}</td>
                                    <td>{{ __data_pt($item->created_at, 0) }}</td>
                                    <td>{{ $item->cnpj_contratante }}</td>
                                    <td>{!! $item->estadoEmissao($item->estado_emissao) !!}</td>
                                    <td>{{ $item->chave }}</td>
                                    <td>{{ $item->mdfe_numero > 0 ? $item->mdfe_numero : '--' }}</td>
                                    <td>{{ $item->veiculoTracao->marca }} - {{ $item->veiculoTracao->placa }} </td>
                                    <td>{{ $item->quantidade_carga }}</td>
                                    <td>{{ __moeda( $item->valor_carga) }}</td>
                                    <td>
                                        @if($item->api)
                                        <span class="text-success">API</span>
                                        @else
                                        <span class="text-primary">Painel</span>
                                        @endif
                                    </td>
                                    <td width="130">
                                        <a class="btn btn-primary btn-sm" title="Download XML" href="{{ route('contador-empresa-mdfe.download', [$item->id]) }}">
                                            <i class="ri-file-download-fill"></i>
                                        </a>

                                        <a target="_blank" class="btn btn-dark btn-sm" title="Danfe" href="{{ route('contador-empresa-mdfe.damdfe', [$item->id]) }}">
                                            <i class="ri-printer-fill"></i>
                                        </a>
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
                <div class="row">
                    
                    @if($contXml > 0)
                    <div class="col-12">

                        <h5 class="mt-2 float-end">Total de arquivos XML: <strong class="text-primary">{{ $contXml }}</strong></h5>
                        <br><br>
                        <a class="btn btn-dark float-end" href="{{ route('contador-empresa-mdfe-zip', ['start_date='.request()->start_date, 'end_date='.request()->end_date]) }}">
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


