@extends('layouts.app', ['title' => 'Pedido Nuvem Shop #'.$item->pedido_id])
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
                    <a href="{{ route('nuvem-shop-pedidos.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                <div class="row mb-2">

                    <div class="col-md-3 col-6">
                        <h5><strong class="text-danger">#{{ $item->pedido_id }}</strong></h5>
                    </div>
                    <div class="col-md-3 col-6">
                        <h5>Data do pedido: <strong class="text-primary">{{ __data_pt($item->data) }}</strong></h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Data de cadastro no sistema: <strong class="text-primary">{{ __data_pt($item->created_at) }}</strong></h5>
                    </div>


                    <div class="col-md-3 col-6">
                        <h5>Valor Total: <strong class="text-primary">R$ {{ __moeda($item->total) }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Valor Entrega: <strong class="text-primary">R$ {{ __moeda($item->valor_frete) }}</strong> </h5>
                    </div>

                    <div class="col-md-3 col-6">
                        <h5>Desconto: <strong class="text-primary">R$ {{ __moeda($item->desconto) }}</strong> </h5>
                    </div>

                </div>

                <a class="btn btn-primary btn-sm d-print-none" href="javascript:window.print()" ><i class="ri-printer-line d-print-none"></i>
                    Imprimir
                </a>
                @if($item->nfe_id == 0)
                <a class="btn btn-success btn-sm d-print-none" href="{{ route('nuvem-shop-pedidos.gerar-nfe', $item->id) }}">
                    <i class="ri-file-text-line"></i>
                    Gerar NFe
                </a>
                @else
                <a class="btn btn-success btn-sm d-print-none" href="{{ route('nfe.show', $item->nfe_id) }}">
                    <i class="ri-file-text-line"></i>
                    Ver NFe
                </a>
                @endif

            </div>

            <div class="row mt-2">
                <h4>Itens do pedido</h4>
                <div class="table-responsive-sm">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Valor unitário</th>
                                <th>Sub total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->itens as $i)
                            <tr>
                                <td>{{ $i->produto->nome }}</td>
                                <td>{{ number_format($i->quantidade, 0) }}</td>
                                <td>{{ __moeda($i->valor_unitario) }}</td>
                                <td>{{ __moeda($i->sub_total) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-4">

                <div class="col-md-6 col-12">
                    <h4>
                        Cliente: <strong>{{ $item->cliente->razao_social }}</strong>
                        @if($item->cliente)
                        <a href="{{ route('clientes.edit', [$item->cliente->id]) }}" class="btn btn-warning btn-sm d-print-none">
                            <i class="ri-edit-line"></i>
                        </a>
                        @else
                        <button class="btn btn-dark btn-sm d-print-none" data-bs-toggle="modal" data-bs-target="#modal-cliente">Atribuir cliente</button>
                        @endif
                    </h4>
                    <h4>ID: <strong>{{ $item->cliente->nuvem_shop_id }}</strong></h4>

                </div>
                <div class="col-md-6 col-12">
                    <h4>Documento cliente: <strong>{{ $item->cliente->cpf_cnpj }}</strong></h4>
                    <h4>Observação: <strong>{{ $item->observacao }}</strong></h4>
                </div>
                <hr>

                <div class="col-md-6 col-12">
                    <h4>Dados de entrega</h4>
                    <h5>Rua: <strong class="text-primary">{{ $item->rua }}</strong></h5>
                    <h5>Bairro: <strong class="text-primary">{{ $item->bairro }}</strong></h5>
                    <h5>Cidade: <strong class="text-primary">{{ $item->cidade }}</strong></h5>
                </div>
                <div class="col-md-6 col-12">
                    <h4><br></h4>
                    <h5>Número: <strong class="text-primary">{{ $item->numero }}</strong></h5>
                    <h5>CEP: <strong class="text-primary">{{ $item->cep }}</strong></h5>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modal-cliente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('mercado-livre-pedidos.set-cliente', [$item->id]) }}">
                @csrf
                @method('put')
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Atribuir cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

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
</script>

@endsection
