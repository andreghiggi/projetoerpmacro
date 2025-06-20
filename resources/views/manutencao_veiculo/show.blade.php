@extends('layouts.app', ['title' => 'Manutenção #' . $item->numero_sequencial])
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
@section('content')

<div class="card mt-1 print">
    <div class="card-body">
        <div class="pl-lg-4">

            <div class="ms">

                <div class="mt-3 d-print-none" style="text-align: right;">
                    <a href="{{ route('manutencao-veiculos.index') }}" class="btn btn-danger btn-sm px-3">
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
                        <h5>Data de início: <strong class="text-muted">{{ __data_pt($item->data_inicio, 0) }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Data de fim: <strong class="text-muted">{{ __data_pt($item->data_fim, 0) }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Total: <strong class="text-primary">R$ {{ __moeda($item->total) }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Desconto: <strong class="text-muted">R$ {{ __moeda($item->desconto) }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Acréscimo: <strong class="text-muted">R$ {{ __moeda($item->acrescimo) }}</strong> </h5>
                    </div>


                    <div class="col-md-3 col-6">
                        <h5>Status da manutenção: 
                            @if($item->estado == 'aguardando')
                            <strong class="text-warning">
                                Aguardando
                            </strong>
                            @elseif($item->estado == 'em_manutencao')
                            <strong class="text-primary">
                                Em manutenção
                            </strong>
                            @else
                            <strong class="text-success">
                                Finalizado
                            </strong>
                            @endif
                        </h5>
                        <button id="btn-alterar-estado" class="btn btn-sm d-print-none" data-toggle="modal" data-target="#modal-estado">alterar estado</button>

                    </div>

                    <form class="row form-alterar-estado mt-2 d-none d-print-none" method="post" action="{{ route('manutencao-veiculos.alterar-estado', [$item->id]) }}">
                        @csrf
                        @method('put')
                        <div class="col-md-3">
                            {!!Form::select('estado', 'Estado',
                            [
                            'aguardando' => 'Aguardando',
                            'em_manutencao' => 'Em manutenção',
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
                @if($item->conta_pagar_id == 0)
                <a class="btn btn-success btn-sm d-print-none" href="{{ route('manutencao-veiculos.gerar-conta-pagar', $item->id) }}">
                    <i class="ri-file-text-line"></i>
                    Gerar Conta a Pagar
                </a>
                @else
                <a class="btn btn-success btn-sm d-print-none" href="{{ route('conta-pagar.edit', $item->conta_pagar_id) }}">
                    <i class="ri-file-text-line"></i>
                    Ver Conta
                </a>
                @endif

                
            </div>

            <div class="row mt-2">
                <h4>Serviços</h4>
                <div class="table-responsive-sm">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Serviço</th>
                                <th>Quantidade</th>
                                <th>Valor unitário</th>
                                <th>Subtotal</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->servicos as $i)
                            <tr>
                                <td>{{ $i->servico->nome }}</td>
                                <td>{{ __moeda($i->quantidade) }}</td>
                                <td>{{ __moeda($i->valor_unitario) }}</td>
                                <td>{{ __moeda($i->sub_total) }}</td>
                                <td>{{ $i->observacao }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Soma</td>
                                <td colspan="3">{{ __moeda($item->servicos->sum('sub_total')) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row mt-2">
                <h4>Produtos</h4>
                <div class="table-responsive-sm">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Valor unitário</th>
                                <th>Subtotal</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->produtos as $i)
                            <tr>
                                <td>{{ $i->produto->nome }}</td>
                                <td>{{ __moeda($i->quantidade) }}</td>
                                <td>{{ __moeda($i->valor_unitario) }}</td>
                                <td>{{ __moeda($i->sub_total) }}</td>
                                <td>{{ $i->observacao }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Soma</td>
                                <td colspan="3">{{ __moeda($item->produtos->sum('sub_total')) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row mt-4">

                <div class="col-md-6 col-12">
                    <h5>Fornecedor: <strong>{{ $item->fornecedor->info }}</strong></h5>
                    <h5>Email: <strong>{{ $item->fornecedor->email }}</strong></h5>
                    <h5>Telefone: <strong>{{ $item->fornecedor->telefone }}</strong></h5>
                    <h5>Data de cadastro: <strong>{{ __data_pt($item->fornecedor->created_at) }}</strong></h5>
                </div>
                <div class="col-md-6 col-12">
                    <h5>Rua: <strong>{{ $item->fornecedor->rua }}</strong></h5>
                    <h5>Número: <strong>{{ $item->fornecedor->numero }}</strong></h5>
                    <h5>Bairro: <strong>{{ $item->fornecedor->bairro }}</strong></h5>
                    <h5>CEP: <strong>{{ $item->fornecedor->cep }}</strong></h5>
                    <h5>Cidade: <strong>{{ $item->fornecedor->cidade->info }}</strong></h5>
                </div>
            </div>

            <form class="row mt-4 d-print-none" enctype="multipart/form-data" method="post" action="{{ route('manutencao-veiculos.upload', [$item->id]) }}">
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
                        <form action="{{ route('manutencao-veiculos.destroy-file', $a->id) }}" method="post" id="form-{{$a->id}}">
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

