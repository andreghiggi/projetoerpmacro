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

                        <div class="col-12 mb-3">
                            <h3>Pedido #{{ $pedido->id }}</h3>
                            <a href="{{ route('conecta-venda-pedidos.index') }}" class="btn btn-sm btn-danger float-end">
                                <i class="ri-arrow-left-double-fill"></i> Voltar
                            </a>
                        </div>

                        {{-- Dados do Comprador --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nome do Comprador</label>
                                <div class="form-control-plaintext">{{ $pedido->comprador ?? '--' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email</label>
                                <div class="form-control-plaintext">{{ $pedido->email ?? '--' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Telefone</label>
                                <div class="form-control-plaintext">{{ $pedido->telefone ?? '--' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">CPF/CNPJ</label>
                                @if($pedido->cpf)
                                    <div class="form-control-plaintext">{{ $pedido->cpf ?? '--' }}</div>
                                @endif
                                @if($pedido->cnpj)
                                    <div class="form-control-plaintext">{{ $pedido->cnpj ?? '--' }}</div>
                                @endif
                            </div>
                        </div>

                        {{-- Tabela de itens do pedido --}}
                        <div class="table-responsive col-12" style="min-height: 300px;">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Referencia</th>
                                    <th>Qtd</th>
                                    <th>Valor unitário</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pedido->itens as $item)
                                    <tr>
                                        <td>{{ $item->nome ?? 'Produto não encontrado' }}</td>
                                        <td>{{ $item->referencia ?? '--' }}</td>
                                        <td>{{ $item->quantidade }}</td>
                                        <td>{{ __moeda($item->valor_unitario) }}</td>
                                        <td>{{ __moeda($item->sub_total) }}</td>
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
