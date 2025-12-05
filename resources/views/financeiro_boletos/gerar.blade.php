@extends('layouts.app', ['title' => 'Gerar Boletos'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <hr class="mt-3">
                <div class="col-lg-12">
                    <p>Total de registros: <strong class="total-boletos">{{ sizeof($data) }}</strong></p>
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Empresa</th>
                                    <th>Email</th>
                                    <th>Celular</th>
                                    <th>Plano</th>
                                    <th>Valor</th>
                                    <th>Data de vencimento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr class="tr_{{ $item->id }}">
                                    <td style="width: 600px">
                                        <input class="form-control" readonly value="{{ $item->info }} - {{ $item->asaas_id }}">
                                        <input type="hidden" class="empresa_id" value="{{ $item->id }}">
                                    </td>
                                    <td style="width: 300px">
                                        <input class="form-control" readonly value="{{ $item->email }}">
                                    </td>
                                    <td style="width: 180px">
                                        <input class="form-control" readonly value="{{ $item->celular }}">
                                    </td>
                                    <td style="width: 150px">
                                        <input class="form-control" readonly value="{{ $item->plano->plano->nome }}">
                                    </td>
                                    <td style="width: 150px">
                                        <input type="tel" required name="valor[]" class="form-control moeda valor" value="{{ __moeda($item->plano->valor) }}">
                                    </td>
                                    <td>
                                        <input type="date" required name="vencimento[]" class="form-control" value="{{ date('Y-m') }}-{{ $item->dia_vencimento_boleto }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="mt-4">
                <div class="col-12" style="text-align: right;">
                    <button type="button" class="btn btn-success px-5" id="btn-gerar">Gerar Boleto(s)</button>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript" src="/js/financeiro_boleto.js"></script>
@endsection


