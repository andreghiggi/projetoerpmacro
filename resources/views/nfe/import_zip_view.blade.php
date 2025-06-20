@extends('layouts.app', ['title' => 'Visualizando arquivos'])
@section('css')
<style type="text/css">

</style>
@endsection
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Visualizando arquivos para NFe</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('nfe.import-zip') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>


    </div>
    
    <div class="card-footer">
        <hr>
        <form id="form-import" class="row" method="post" action="{{ route('nfe.import-zip-store-files') }}" enctype="multipart/form-data">
            @csrf

            @if(__countLocalAtivo() > 1)
            <div class="col-md-2 m-2">
                <label for="">Local</label>

                <select id="inp-local_id" required class="select2 class-required" data-toggle="select2" name="local_id">
                    <option value="">Selecione</option>
                    @foreach(__getLocaisAtivoUsuario() as $local)
                    <option @isset($item) @if($item->local_id == $local->id) selected @endif @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <input id="inp-local_id" type="hidden" value="{{ __getLocalAtivo() ? __getLocalAtivo()->id : '' }}" name="local_id">
            @endif

            <div class="row">
                @foreach($data as $key => $d)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <input type="hidden" value="{{json_encode($d)}}" name="data[]">

                                    <h5 class="text-danger">
                                        <input value="{{ $d['chave'] }}" checked class="form-checkbox" type="checkbox" name="file_id[]">
                                        {{ $d['chave'] }}
                                    </h5>

                                </div>
                                <div class="col-md-6 col-12">
                                    <h5 class="text-end">{{ __data_pt($d['data']) }}</h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    Valor total: <strong class="text-primary">R$ {{ __moeda($d['valor_total']) }}</strong>
                                </div>
                                <div class="col-md-4 col-12">
                                    Desconto: <strong class="text-primary">R$ {{ __moeda($d['desconto']) }}</strong>
                                </div>
                                <div class="col-md-4 col-12">
                                    Número NFe: <strong class="text-primary">{{ $d['numero_nfe'] }}</strong>
                                </div>
                            </div>

                            <div class="row">
                                @if($d['cliente'])
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Cliente</h3>

                                        <h5>Razão social: <strong class="text-primary">{{$d['cliente']['razao_social']}}</strong></h5>
                                        <h5>CNPJ/CPF: <strong class="text-primary">{{$d['cliente']['cpf_cnpj']}}</strong></h5>
                                        <h5>IE/RG: <strong class="text-primary">{{$d['cliente']['ie_rg']}}</strong></h5>
                                        <h5>Endereço: <strong class="text-primary">{{$d['cliente']['rua']}}, {{$d['cliente']['numero']}} - {{$d['cliente']['bairro']}}</strong></h5>
                                        <h5>Cidade: <strong class="text-primary">{{$d['cliente']['cidade_info']}}</strong></h5>
                                    </div>
                                </div>
                                @endif

                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Produtos</h3>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-centered mb-0">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>#</th>
                                                        <th style="width: 500px">Nome</th>
                                                        <th>CFOP</th>
                                                        <th>Unidade</th>
                                                        <th>Quantidade</th>
                                                        <th>Valor unitário</th>
                                                        <th>Subtotal</th>
                                                        <th>NCM</th>
                                                        <th>Código de barras</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($d['produtos'] as $p)
                                                    <tr>
                                                        <td>{{ $p['codigo'] }}</td>
                                                        <td>{{ $p['nome'] }}</td>
                                                        <td>{{ $p['cfop_estadual'] }}/{{ $p['cfop_outro_estado'] }}</td>
                                                        <td>{{ $p['unidade'] }}</td>
                                                        <td>{{ $p['quantidade'] }}</td>
                                                        <td>{{ __moeda((float)$p['valor_unitario']) }}</td>
                                                        <td>{{ __moeda((float)$p['sub_total']) }}</td>
                                                        <td>{{ $p['ncm'] }}</td>
                                                        <td>{{ $p['codigo_barras'] }}</td>

                                                    </tr>
                                                    @endforeach
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Fatura</h3>
                                        <div class="row">
                                            @foreach($d['fatura'] as $f)
                                            <div class="col-md-4 col-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5>Vencimento: <strong>{{ __data_pt($f['vencimento'], 0) }}</strong></h5>
                                                        <h5>Valor: <strong>R$ {{ __moeda((float)$f['valor_parcela']) }}</strong></h5>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="col-12" style="text-align: right; margin-right: 10px;">
                    <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
                </div>
            </div>
            
        </form>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript">

</script>
@endsection
