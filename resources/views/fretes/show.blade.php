@extends('layouts.app', ['title' => 'Frete #' . $item->numero_sequencial])
@section('css')
<style type="text/css">
    @page { size: auto;  margin: 0mm; }

    @media print {
        .print{
            margin: 10px;
        }
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

    .card-body strong{
        color: #8833FF;
    }
</style>
@endsection
@section('content')

<div class="card mt-1 print">
    <div class="card-body">
        <div class="pl-lg-4">

            <div class="ms">

                <div class="mt-3 d-print-none" style="text-align: right;">
                    <a href="{{ route('fretes.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                <div class="row mb-2">

                    <div class="col-md-3 col-6">
                        <h5><strong class="text-danger">#{{ $item->numero_sequencial }}</strong></h5>
                    </div>
                    <div class="col-md-3 col-6">
                        <h5>Data de cadastro: <strong class="text-muted">{{ __data_pt($item->created_at) }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Data de início viagem: <strong class="text-muted">{{ __data_pt($item->data_inicio, 0) }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Data de fim viagem: <strong class="text-muted">{{ __data_pt($item->data_fim, 0) }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Horário início viagem: <strong class="text-muted">{{ $item->horario_inicio }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Horário fim viagem: <strong class="text-muted">{{ $item->horario_fim }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Distância: <strong class="text-muted">{{ $item->distancia_km }} KM</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Valor do Frete: <strong class="text-primary">R$ {{ __moeda($item->total) }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Desconto: <strong class="text-muted">R$ {{ __moeda($item->desconto) }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Acréscimo: <strong class="text-muted">R$ {{ __moeda($item->acrescimo) }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Cidade de carregamento: <strong class="text-muted">{{ $item->cidadeCarregamento->info }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Cidade de descarregamento: <strong class="text-muted">{{ $item->cidadeDescarregamento->info }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Status do frete: 
                            @if($item->estado == 'em_carregamento')
                            <strong class="text-warning">
                                Em carregamento
                            </strong>
                            @elseif($item->estado == 'em_viagem')
                            <strong class="text-primary">
                                Em viagem
                            </strong>
                            @else
                            <strong class="text-success">
                                Finalizado
                            </strong>
                            @endif
                        </h5>
                        <button id="btn-alterar-estado" class="btn btn-sm d-print-none" data-toggle="modal" data-target="#modal-estado">alterar estado</button>

                    </div>

                    <form class="row form-alterar-estado mt-2 d-none d-print-none" method="post" action="{{ route('fretes.alterar-estado', [$item->id]) }}">
                        @csrf
                        @method('put')
                        <div class="col-md-3">
                            {!!Form::select('estado', 'Estado',
                            [
                            'em_carregamento' => 'Em carregamento',
                            'em_viagem' => 'Em viagem',
                            'finalizado' => 'Finalizado',
                            ])
                            ->value($item->estado)
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3">
                            <br>
                            <button class="btn btn-success">Alterar</button>
                        </div>
                    </form>

                </div>

                <hr>
                <a class="btn btn-primary btn-sm d-print-none" href="javascript:window.print()" ><i class="ri-printer-line d-print-none"></i>
                    Imprimir
                </a>
                @if($item->conta_receber_id == 0)
                <a class="btn btn-success btn-sm d-print-none" href="{{ route('fretes.gerar-conta-receber', $item->id) }}">
                    <i class="ri-file-text-line"></i>
                    Gerar Conta a Receber
                </a>
                @else
                <a class="btn btn-success btn-sm d-print-none" href="{{ route('conta-receber.edit', $item->conta_receber_id) }}">
                    <i class="ri-file-text-line"></i>
                    Ver Conta
                </a>
                @endif

                
            </div>

            <div class="row mt-2">
                <h4>Despesas</h4>
                <div class="table-responsive-sm">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Tipo despesa</th>
                                <th>Fornecedor</th>
                                <th>Valor</th>
                                <th>Observação</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->despesas as $i)
                            <tr>
                                <td>{{ $i->tipoDespesaFrete->nome }}</td>
                                <td>{{ $i->fornecedor ? $i->fornecedor->info : '' }}</td>
                                <td>{{ __moeda($i->valor) }}</td>
                                <td>{{ $i->observacao }}</td>
                                <td>
                                    @if($i->fornecedor)
                                    @if($i->conta_pagar_id)
                                    <a class="btn btn-success btn-sm d-print-none" href="{{ route('conta-pagar.edit', $i->conta_pagar_id) }}">
                                        <i class="ri-file-text-line"></i>
                                        Ver Conta
                                    </a>
                                    @else
                                    <a class="btn btn-dark btn-sm d-print-none" href="{{ route('despesa-frete.gerar-conta-pagar', $i->id) }}">
                                        <i class="ri-file-text-line"></i>
                                        Gerar Conta a Pagar
                                    </a>
                                    @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Soma</td>
                                <td colspan="3">{{ __moeda($item->total_despesa) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row mt-4">

                <div class="col-md-6 col-12">
                    <h5>Cliente: <strong>{{ $item->cliente->info }}</strong></h5>
                    <h5>Email: <strong>{{ $item->cliente->email }}</strong></h5>
                    <h5>Telefone: <strong>{{ $item->cliente->telefone }}</strong></h5>
                    <h5>Data de cadastro: <strong>{{ __data_pt($item->cliente->created_at) }}</strong></h5>
                </div>
                <div class="col-md-6 col-12">
                    <h5>Rua: <strong>{{ $item->cliente->rua }}</strong></h5>
                    <h5>Número: <strong>{{ $item->cliente->numero }}</strong></h5>
                    <h5>Bairro: <strong>{{ $item->cliente->bairro }}</strong></h5>
                    <h5>CEP: <strong>{{ $item->cliente->cep }}</strong></h5>
                    <h5>Cidade: <strong>{{ $item->cliente->cidade->info }}</strong></h5>
                </div>
            </div>

            <form class="row mt-4 d-print-none" enctype="multipart/form-data" method="post" action="{{ route('fretes.upload', [$item->id]) }}">
                @csrf
                <div class="col-md-3 file-certificado">
                    {!! Form::file('file', 'Procurar arquivo')
                    ->attrs(['accept' => '.pdf, image/*']) !!}
                    <span class="text-danger" id="filename"></span>
                </div>
                <div class="col-md-3">
                    <br>
                    <button type="submit" class="btn btn-success">Salvar arquivo</button>
                </div>
                
            </form>
            <div class="row d-print-none">
                <hr>
                <h5>Anexos</h5>
                <div class="row">
                    @foreach($item->anexos as $key => $a)
                    <div class="col-md-4">
                        <form action="{{ route('fretes.destroy-file', $a->id) }}" method="post" id="form-{{$a->id}}">
                            @method('delete')
                            @csrf
                            <a target="_blank" href="{{ $a->file }}">Conteudo anexado {{$key+1}}</a><br>
                            <button class="btn btn-sm btn-danger btn-delete">remover</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>


@endsection
@section('js')
<script type="text/javascript">
    $('#btn-alterar-estado').click(() => {
        $('.form-alterar-estado').removeClass('d-none')
    })
</script>
@endsection

