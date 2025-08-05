@extends('layouts.app', ['title' => 'Editar Pedido - Conecta Venda'])

@section('css')
    <style type="text/css">
        .card-hover:hover {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="mt-3">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card">
                    <div class="card-body">

                        <div class="col-12 mb-4">
                            <h3>Pedido #{{ $pedido->id }}</h3>
                            <a href="{{ route('conecta-venda-pedidos.index') }}" class="btn btn-sm btn-danger float-end">
                                <i class="ri-arrow-left-double-fill"></i> Voltar
                            </a>
                        </div>
                        {{-- Dados do Cliente --}}
                        <h5 class="col my-3 bold">
                            Dados Do Cliente
                        </h5>
                        <div class="col-12 mb-6">
                            <div class="row mb-6">
                                <div class="col-sm-lg-2">
                                    <label class="form-label fw-bold">Cliente</label>
                                    <div class="form-control-plaintext">{{ $pedido->comprador ?? '--' }}</div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-bold">Telefone</label>
                                    <div class="form-control-plaintext">{{ $pedido->telefone ?? '--' }}</div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-bold">Email</label>
                                    <div class="form-control-plaintext">{{ $pedido->email ?? '--' }}</div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label fw-bold">CPF/CNPJ</label>
                                    <div class="form-control-plaintext">{{ $pedido->cpf ?? $pedido->cnpj ?? '--' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-6">
                            <h5 class="col my-3 bold">
                                Endereco
                            </h5>
                            <div class="row mb-6">
                                <div class="col-sm-3">
                                    <label class="form-label fw-bold">Cidade</label>
                                    <div class="form-control-plaintext">{{ $pedido->cidade ?? '--' }} / {{ $pedido->uf ?? '--' }}</div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label fw-bold">Bairro</label>
                                    <div class="form-control-plaintext">{{ $pedido->bairro ?? '--' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Endereço</label>
                                    <div class="form-control-plaintext">{{ $pedido->endereco ?? '--' }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">CEP</label>
                                    <div class="form-control-plaintext">{{ $pedido->cep ?? '--' }}</div>
                                </div>
                            </div>
                        </div>

                        <h5 class="col my-3 bold">
                            Catalogo
                        </h5>
                        <div class="col-12 mt-4">
                            <div class="row mb-lg-2">
                                <div class="col-sm-4">
                                    <label class="form-label fw-bold">Catálogo</label>
                                    <div class="form-control-plaintext">{{ $pedido->catalogo ?? '--' }}</div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-bold">Vendedor</label>
                                    <div class="form-control-plaintext">{{ $pedido->vendedor ?? '--' }}</div>
                                </div>
                            </div>
                        </div>

                        <h5 class="col my-3 bold">
                            Cupom
                        </h5>
                        <div class="col-12 mt-4">
                            <div class="row mb-lg-2">
                                <div class="col-sm-4">
                                    <label class="form-label fw-bold">Desconto</label>
                                    <div class="form-control-plaintext">{{ $pedido->desconto ?? '' }}</div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-bold">Valor Desconto</label>
                                    <div class="form-control-plaintext">R$ {{ $pedido->valor_desconto ?? '0.0' }}</div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label fw-bold">Total Pedido</label>
                                    <div class="form-control-plaintext">R$ {{ $pedido->valor_pagamento ?? '' }}</div>
                                </div>
                            </div>
                        </div>



                        <h5 class="col my-3 bold">
                            Produtos
                        </h5>
                        <div class="table-responsive col-12" style="min-height: 300px;">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Variação</th>
                                    <th>Referência</th>
                                    <th>Qtd</th>
                                    <th>Observação</th>
                                    <th>Valor unitário</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pedido->produtos as $produto)
                                    <tr>
                                        <td>{{ $produto->nome ?? 'Produto não encontrado' }}</td>
                                        <td>{{ $produto->variacoes->descricao ?? '--' }}</td>
                                        <td>{{ $produto->referencia ?? '--' }}</td>
                                        <td>{{ $produto->quantidade }}</td>
                                        <td>{{ $produto->observacao ?? '--' }}</td>
                                        <td>{{ __moeda($produto->valor_unitario) }}</td>
                                        <td>{{ __moeda($produto->sub_total) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Observação Geral do Pedido --}}
                        <div class="col-12 mt-4">
                            <label for="observacao_geral" class="form-label fw-bold">Observação do Pedido</label>
                            <textarea name="observacao_geral" id="observacao_geral" rows="3" class="form-control" readonly>{{ $pedido->observacao }}</textarea>
                        </div>

                        {{-- Botões ou ações finais (opcional) --}}
                        <hr>
                        <div class="col-12 text-end">
                            {{-- Ex: botão de finalizar pedido, se quiser reativar --}}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
