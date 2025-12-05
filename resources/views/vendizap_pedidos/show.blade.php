@extends('layouts.app', ['title' => 'Pedido VendiZap #'.$data->id])
@section('css')
<style type="text/css">
    @page { size: auto;  margin: 0mm; }

    @media print {
        .print{
            margin: 10px;
        }
    }
</style>
@endsection
@section('content')

<div class="card mt-1 print">
    <div class="card-body">
        <div class="pl-lg-4">

            <div class="ms">

                <div class="mt-3 d-print-none" style="text-align: right;">
                    <a href="{{ route('vendizap-pedidos.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                <div class="row mb-2">

                    <div class="col-md-3 col-6">
                        <h5><strong class="text-danger">#{{ $data->id }}</strong></h5>
                    </div>
                    <div class="col-md-3 col-6">
                        <h5>Data do pedido: <strong class="text-primary">{{ __data_pt($data->data) }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Data de Cadastro no Sistema: <strong class="text-primary">{{ __data_pt($pedido->created_at) }}</strong></h5>
                    </div>


                    <div class="col-md-3 col-6">
                        <h5>Valor Total: <strong class="text-primary">R$ {{ __moeda($data->valorPedido) }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Valor Entrega: <strong class="text-primary">R$ {{ __moeda($data->taxaEntrega ? $data->taxaEntrega : 0) }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Valor de Retirada: <strong class="text-primary">R$ {{ __moeda($data->taxaRetirada ? $data->taxaRetirada : 0) }}</strong> </h5>
                    </div>

                </div>

                <a class="btn btn-primary btn-sm d-print-none" href="javascript:window.print()" ><i class="ri-printer-line d-print-none"></i>
                    Imprimir
                </a>
                @if($pedido->nfe_id == 0)
                <a class="btn btn-success btn-sm d-print-none" href="{{ route('vendizap-pedidos.gerar-nfe', $pedido->id) }}">
                    <i class="ri-file-text-line"></i>
                    Gerar NFe
                </a>
                @else
                <a class="btn btn-success btn-sm d-print-none" href="{{ route('nfe.show', $pedido->nfe_id) }}">
                    <i class="ri-file-text-line"></i>
                    Ver NFe
                </a>
                @endif

            </div>

            <div class="row mt-2">
                <h4>Itens do Pedido</h4>
                <div class="table-responsive-sm">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Produto</th>
                                <th>Observação</th>
                                <th>Quantidade</th>
                                <th>Valor unitário</th>
                                <th>Sub total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data->itens as $i)
                            <tr>
                                <td>
                                    {{ $i->descricao }} {{ $i->variacaoNome }}
                                    - {{ $i->unidadeVenda }}
                                </td>
                                <td>{{ $i->observacaoProduto }}</td>
                                <td>{{ number_format($i->quantidade, 0) }}</td>
                                <td>{{ __moeda($i->precoPromocional ? $i->precoPromocional : $i->preco) }}</td>
                                <td>{{ __moeda($i->valorLiquido) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-4">

                <div class="col-md-6 col-12">
                    <h4>
                        Cliente: <strong>{{ $data->cliente->nome }}</strong>
                        @if($pedido->cliente)
                        <a href="{{ route('clientes.edit', [$pedido->cliente->id]) }}" class="btn btn-warning btn-sm d-print-none">
                            <i class="ri-edit-line"></i>
                        </a>
                        @else
                        <button class="btn btn-dark btn-sm d-print-none" data-bs-toggle="modal" data-bs-target="#modal-cliente">
                            <i class="ri-user-3-line"></i>
                            Atribuir Cliente
                        </button>
                        @endif
                    </h4>
                    <h4>Telefone: <strong>{{ $data->cliente->telefone }}</strong></h4>

                </div>
                <div class="col-md-6 col-12">
                    <h4>Documento do Cliente: <strong>{{ $data->cliente->documento }}</strong></h4>
                    <h4>Observação: <strong>{{ $data->observacao }}</strong></h4>
                    <h4>Email: <strong>{{ $data->email }}</strong></h4>
                </div>

                @if(isset($data->dadosExcursao))
                <hr>
                <div class="row">
                    <h6 class="col-12">{{ $data->dadosExcursao->nome }}</h6>
                    @foreach($data->dadosExcursao->respostas as $r)
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>{{ $r->pergunta }}</h5>
                                <h5>{{ $r->resposta }}</h5>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                <hr>

                @if($data->cliente->endereco)
                <div class="col-md-6 col-12">
                    <h4>Dados de entrega</h4>
                    <h5>Rua: <strong class="text-primary">{{ $data->cliente->endereco->logradouro }}</strong></h5>
                    <h5>Bairro: <strong class="text-primary">{{ $data->cliente->endereco->bairro }}</strong></h5>
                    <h5>Cidade: <strong class="text-primary">{{ $data->cliente->endereco->cidade }} ({{ $data->cliente->endereco->estado }})</strong></h5>
                </div>
                <div class="col-md-6 col-12">
                    <h4><br></h4>
                    <h5>Número: <strong class="text-primary">{{ $data->cliente->endereco->numero }}</strong></h5>
                    <h5>CEP: <strong class="text-primary">{{ $data->cliente->endereco->cep }}</strong></h5>
                    <h5>Comlpemento: <strong class="text-primary">{{ $data->cliente->endereco->complemento }}</strong></h5>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modal-cliente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('vendizap-pedidos.set-cliente', [$pedido->id]) }}">
                @csrf
                @method('put')
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Atribuir cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-4 mb-2">
                            <button type="button" id="btn-novo-cliente" class="btn btn-sm btn-success">Cadastrar cliente</button>
                        </div>
                        <hr>
                        <div class="col-md-12">
                            {!!Form::select('cliente_id', 'Cliente')
                            ->required()

                            !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success">Atribuir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-novo-cliente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="post" action="{{ route('vendizap-pedidos.store-cliente', [$pedido->id]) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Novo cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">

                        <div class="col-md-3">
                            {!!Form::text('cpf_cnpj', 'CPF/CNPJ')->attrs(['class' => 'cpf_cnpj'])->required()
                            ->value($data->cliente->documento)
                            !!}
                        </div>
                        <div class="col-md-4">
                            {!!Form::text('razao_social', 'Nome')->attrs(['class' => ''])->required()
                            ->value($data->cliente->nome)
                            !!}
                        </div>
                        
                        <div class="col-md-2">
                            {!!Form::text('ie', 'IE')->attrs(['class' => 'ie ignore'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('telefone', 'Telefone')->attrs(['class' => 'fone'])->required()
                            ->value($data->cliente->telefone)
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('contribuinte', 'Contribuinte', [0 => 'Não', 1 => 'Sim'])->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('consumidor_final', 'Consumidor Final', [1 => 'Sim', 0 => 'Não'])->attrs(['class' => 'form-select'])->required()
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('status', 'Ativo', [ 1 => 'Sim', 0 => 'Não'])->attrs(['class' => 'form-select'])->required()
                            !!}
                        </div>
                        <div class="col-md-4">
                            {!! Form::text('email', 'Email')->attrs(['class' => 'ignore'])->type('email')
                            ->value($data->email)
                            !!}
                        </div>

                        <div class="col-md-4">
                            {!!Form::select('cidade_id', 'Cidade')
                            ->attrs(['class' => 'select2'])
                            ->required()
                            ->options($cidade ? [$cidade->id => $cidade->info] : [])
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::text('rua', 'Rua')->attrs(['class' => ''])->required()
                            ->value($data->cliente->endereco ? $data->cliente->endereco->logradouro : '')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::text('numero', 'Número')->attrs(['class' => ''])->required()
                            ->value($data->cliente->endereco ? $data->cliente->endereco->numero : '')
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::text('cep', 'CEP')->attrs(['class' => 'cep'])->required()
                            ->value($data->cliente->endereco ? $data->cliente->endereco->cep : '')
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::text('bairro', 'Bairro')->attrs(['class' => ''])->required()
                            ->value($data->cliente->endereco ? $data->cliente->endereco->bairro : '')
                            !!}
                        </div>
                        <div class="col-md-5">
                            {!!Form::text('complemento', 'Complemento')->attrs(['class' => 'ignore'])
                            ->value($data->cliente->endereco ? $data->cliente->endereco->complemento : '')
                            !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    $("#inp-cliente_id").select2({
        minimumInputLength: 2,
        language: "pt-BR",
        placeholder: "Digite para buscar o cliente",
        theme: "bootstrap4",
        dropdownParent: $('#modal-cliente'),
        ajax: {
            cache: true,
            url: path_url + "api/clientes/pesquisa",
            dataType: "json",
            data: function (params) {
                console.clear();
                var query = {
                    pesquisa: params.term,
                    empresa_id: $("#empresa_id").val(),
                };
                return query;
            },
            processResults: function (response) {
                var results = [];

                $.each(response, function (i, v) {
                    var o = {};
                    o.id = v.id;

                    o.text = v.razao_social + " - " + v.cpf_cnpj;
                    o.value = v.id;
                    results.push(o);
                });
                return {
                    results: results,
                };
            },
        },
    });

    $('#btn-novo-cliente').click(() => {
        $('#modal-cliente').modal('hide')
        $('#modal-novo-cliente').modal('show')
    })
</script>

@endsection
