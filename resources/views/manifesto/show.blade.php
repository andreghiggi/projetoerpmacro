@extends('layouts.app', ['title' => 'Manifesto'])
@section('content')
<div class="page-content">
    <div class="card border-top border-0 border-4 border-primary">
        <div class="m-3">
            <div class="col-lg-12">
                <input type="hidden" name="id" value="{{{ isset($cliente) ? $cliente->id : 0 }}}">
                <div class="card-header">
                    <h3 class="card-title">Manifesto XML</h3>
                </div>
                <div class="row">
                    {!!Form::open()
                    ->post()
                    ->route('dfe.storeCompra')
                    ->multipart()!!}
                    <div class="card col-xl-12 m-3">
                        <div class="card-body">
                            <h5 class="center-align mt-2">Nota Fiscal: <strong class="text-primary">{{$nNf}}</strong></h5>
                            <h5 class="center-align">Chave: <strong class="text-primary">{{$chave}}</strong></h5>
                            @if(count($fornecedor) > 0)
                            <div class="row">
                                <div class="col-xl-12">
                                    <h6 class="text-success">Dados atualizados do fornecedor</h6>
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Fornecedor: <strong>{{$forn->razao_social}}</strong></h5>
                                    <h5>Nome Fantasia: <strong>{{$forn->nome_fantasia}}</strong></h5>
                                    <h5>CNPJ: <strong>{{$forn->cpf_cnpj}}</strong></h5>
                                    <h5>IE: <strong>{{$forn->ie_rg}}</strong></h5>
                                </div>

                                <div class="col-md-6">
                                    <h5>Logradouro: <strong>{{$forn->rua}}</strong></h5>
                                    <h5>Numero: <strong>{{$forn->numero}}</strong></h5>
                                    <h5>Bairro: <strong>{{$forn->bairro}}</strong></h5>
                                    <h5>CEP: <strong>{{$forn->cep}}</strong></h5>
                                    <h5>Fone: <strong>{{$forn->fone}}</strong></h5>
                                </div>
                            </div>
                            <input type="hidden" name="fornecedor_id" id="idFornecedor" value="{{$forn->id}}">
                            <input type="hidden" name="valor_total" value="{{$dfe->valor}}">
                            <input type="hidden" name="chave" id="chave" value="{{$chave}}">
                            <input type="hidden" name="nNf" id="nNf" value="{{$nNf}}">
                            <input type="hidden" name="dfe_id" id="" value="{{$dfe->id}}">

                            {{-- <input type="hidden" name="pathXml" id="pathXml" value="{{$pathXml}}">
                            <input type="hidden" name="data_emissao" id="data_emissao" value="{{$dadosNf['data_emissao']}}">
                            <input type="hidden" name="vDesc" id="vDesc" value="{{$dadosNf['vDesc']}}">
                            <input type="hidden" id="prodSemRegistro" value="{{$dadosNf['contSemRegistro']}}">
                            --}}
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <div class="row">

                            <div class="col-xl-12 m-3">
                                <p class="text-danger">* Produtos em vermelho não possui cadastro no sistema.</p>
                                <h5>Itens da NFe: <strong class="text-info">{{sizeof($itens)}}</strong></h5>
                                <div id="kt_datatable" class="table-responsive">
                                    <table class="table mb-0 table-striped" style="">
                                        <thead class="datatable-head">
                                            <tr class="" style="left: 0px;">
                                                <th data-field="OrderID" class="datatable-cell datatable-cell-sort"><span style="width: 70px;">#</span></th>
                                                <th data-field="Country" class="datatable-cell datatable-cell-sort"><span style="width: 180px;">Produto</span></th>
                                                <th data-field="ShipDate" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">NCM</span></th>
                                                <th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">CFOP</span></th>
                                                <th data-field="CompanyName" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">CEST</span></th>
                                                <th data-field="Status" class="datatable-cell datatable-cell-sort"><span style="width: 90px;">Cod Barra</span></th>
                                                <th data-field="Type" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">Un. Compra</span></th>
                                                <th data-field="Type" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">Valor</span></th>
                                                <th data-field="Type" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">Qtd</span></th>
                                                <th data-field="Type" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">CFOP Ent.</span></th>
                                                <th data-field="Type" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">Subtotal</span></th>
                                                <th data-field="Actions" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"><span style="width: 80px;">Ações</span></th>
                                            </tr>
                                        </thead>
                                        <tbody class="">
                                            @foreach($itens as $i)
                                            <tr class="" id="tr_{{$i['codigo']}}">
                                                <input type="hidden" class="inp-novo-{{$i['codigo']}} inp-check" value="{{$i['produtoNovo']}}">

                                                <input type="hidden" class="produto_id_{{$i['codigo']}}" name="produto_id[]" value="{{$i['produto_id']}}">
                                                <input type="hidden" name="quantidade[]" value="{{$i['qCom']}}">
                                                <input type="hidden" name="valor_unitario[]" value="{{$i['vUnCom']}}">
                                                <input type="hidden" name="unidade_compra[]" value="{{$i['uCom']}}">
                                                <input type="hidden" name="cfop[]" value="{{$i['CFOP']}}">
                                                <input type="hidden" name="cest[]" value="{{$i['CEST']}}">

                                                <td>{{$i['codigo']}}</td>
                                                <td><span id="n_{{$i['codigo']}}" class="{{$i['produtoNovo'] ? 'text-danger' : ''}} nome">{{$i['xProd']}}</span></td>
                                                <td><span class="ncm" style="width: 80px;">{{$i['NCM']}}</span></td>
                                                <td><span class="cfop" style="width: 80px;">{{$i['CFOP']}}</span></td>
                                                <td><span class="cest" style="width: 80px;">{{$i['CEST']}}</span></td>
                                                <td><span class="codBarras" style="width: 90px;">{{$i['codBarras']}}</span></td>
                                                <td><span class="unidade" style="width: 80px;">{{$i['uCom']}}</span></td>
                                                <td class="mt-3">{{__moeda((float)$i['vUnCom'])}}</td>
                                                <td class="mt-5"><span id="qtd_aux_{{$i['codigo']}}" class="quantidade mt-3">{{$i['qCom']}}</span></td>
                                                <td>
                                                    <span class="" id="cfop_entrada_{{$i['codigo']}}" style="">
                                                        <input id="cfop_entrada_input" class="cfop form-control" style="width: 60px;" type="text" value="{{$i['CFOP']}}" name="">
                                                    </span>
                                                </td>
                                                <td class="">{{__moeda((float) $i['qCom'] * (float) $i['vUnCom'])}}</td>
                                                <th>
                                                    <span>
                                                        <a class="@if(!$i['produtoNovo']) d-none @endif btn-cad-{{$i['codigo']}}" id="th_acao1_{{$i['codigo']}}" onclick="cadProd('{{$i['codigo']}}','{{$i['xProd']}}','{{$i['codBarras']}}','{{$i['NCM']}}','{{$i['CFOP']}}','{{$i['uCom']}}','{{$i['vUnCom']}}','{{$i['qCom']}}', '{{$i['CFOP']}}','{{$i['CEST']}}')" href="javascript:;" data-bs-toggle="modal" data-bs-target="#modal-produto">
                                                            <span class="btn btn-success btn-sm">
                                                                <i class="bx bx-plus"></i>
                                                            </span>
                                                        </a>
                                                    </span>
                                                </th>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="total" id="total" class="moeda" value="{{$infos['vProd']}}">

                    <div class="row m-2">
                        <div class="col-xl-6">
                            <h4>Total: <strong id="valorDaNF" class="blue-text">R$ {{ __moeda((float)$infos['vProd']) }}</strong></h4>
                        </div>
                        <div class="col-xl-3">
                        </div>
                        <div class="col-xl-3">
                            @if($dfe->compra_id == 0)
                            <button type="submit" disabled id="btn-salvar" style="width: 100%" class="btn btn-success spinner-white spinner-right">
                                <i class=""></i>
                                <span class="">Salvar como compra</span>
                            </button>
                            @else
                            @endif
                        </div>

                    </div>
                    {!!Form::close()!!}

                    <div class="col-xl-12 mt-3 m-3">
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-body">
                                <input type="hidden" id="fatura" value="{{json_encode($fatura)}}">
                                {!!Form::open()
                                ->post()
                                ->route('dfe.storeFatura')
                                !!}
                                <input type="hidden" name="fornecedor_id" id="" value="{{$forn->id}}">
                                <input type="hidden" name="dfe_id" id="" value="{{$dfe->id}}">
                                <div class="table-responsive">
                                    <h4>Fatura</h4>
                                    <table class="table mb-0 table-striped table-dynamic">
                                        <thead>
                                            <tr>
                                                <th>Vencimento</th>
                                                <th>Valor</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody id="body" class="datatable-body">
                                            @isset($fatura)
                                            @foreach ($fatura as $f)
                                            <tr class="dynamic-form">
                                                <td class="col-2">
                                                    <input type="text" class="form-control" name="vencimento[]" value="{{$f['vencimento']}}">
                                                </td>
                                                <td class="col-2">
                                                    <input type="text" class="form-control moeda" name="valor_parcela[]" value="{{$f['valor_parcela']}}">
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td class="col-2">
                                                    <input type="text" class="form-control" name="vencimento[]" value="{{$d['data_emissao']}}">
                                                </td>
                                                <td class="col-2">
                                                    <input type="text" class="form-control moeda" name="valor_parcela[]" value="{{$d['valor']}}">
                                                </td>
                                            </tr>
                                            @endisset
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <br>

                                        @if($dfe->fatura_salva == 0 && sizeof($fatura) > 0)
                                        <button type="submit" class="btn btn-success">
                                            Salvar Fatura no Contas a Pagar
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                {!!Form::close()!!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @section('js')
<script type="text/javascript" src="/js/manifestoDfe.js"></script>
@endsection --}}

{{-- @include('modals._produto', ['not_submit' => true]) --}}

@endsection
