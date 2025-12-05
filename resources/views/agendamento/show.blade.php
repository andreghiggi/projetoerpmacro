@extends('layouts.app', ['title' => 'Agendamento'])
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
<div class="mt-1 print">
    <div class="row">

        <div class="card">
            <div class="card-body">

                <!-- Invoice Logo-->
                <div class="clearfix">
                    <div class="float-start mb-3">

                    </div>
                    <div class="float-end">
                        <h4 class="m-0">{{ $item->nome }}</h4>
                    </div>
                </div>

                <!-- Invoice Detail-->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mt-3" style="line-height: 0.7;">
                            @can('clientes_edit')
                            <a class="btn btn-sm btn-warning mb-2 d-print-none" href="{{ route('clientes.edit', [$item->cliente_id]) }}">
                                <i class="ri-edit-line"></i> Editar cliente
                            </a>
                            @endcan
                            <h4>#{{ $item->numero_sequencial }}</h4>

                            <p>
                                <b>Cliente: <strong class="text-primary">{{ $item->cliente->razao_social }}</strong></b> 
                            </p>

                            <p><b>CPF/CNPJ: <strong class="text-primary">{{ $item->cliente->cpf_cnpj }}</strong></b></p>
                            <p><b>Telefone: <strong class="text-primary">{{ $item->cliente->telefone }}</strong></b></p>
                            <p><b>Total de serviços: <strong class="text-primary">{{ sizeof($item->itens) }}</strong></b></p>
                            <p><b>Desconto: <strong class="text-danger">{{ __moeda($item->desconto) }}</strong></b></p>
                            <p><b>Atendente: <strong class="text-primary">{{ $item->funcionario ? $item->funcionario->nome : '' }}</strong></b></p>
                            
                        </div>

                    </div><!-- end col -->
                    <div class="col-sm-4 offset-sm-2">
                        @can('agendamento_edit')
                        <form method="POST" action="{{ route('agendamentos.update', [$item->id]) }}" class="mt-3 float-sm-end" style="line-height: 1.2;">
                            @method('put')
                            @csrf
                            <div class="col-12">
                                {!!Form::tel('inicio', 'Início')->attrs(['class' => 'timer'])
                                ->value(\Carbon\Carbon::parse($item->inicio)->format('H:i')) !!}
                            </div>
                            <div class="col-12 mt-2">
                                {!!Form::tel('termino', 'Término')->attrs(['class' => 'timer'])
                                ->value(\Carbon\Carbon::parse($item->termino)->format('H:i')) !!}
                            </div>

                            <div class="col-12 mt-2">
                                {!!Form::date('data', 'Data')->attrs(['class' => 'date'])
                                ->value($item->data) !!}
                            </div>

                            <div class="col-12 mt-2 d-print-none">
                                <button class="btn btn-success w-100">
                                    <i class="ri-check-line"></i> Salvar
                                </button>
                            </div>
                        </form>
                        @endcan
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="row mt-4">
                    <div class="col-8">

                    </div>

                    <div class="col-4">
                        <div class="text-sm-end">

                        </div>
                    </div> <!-- end col-->
                </div>    
                <!-- end row -->        

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-centered table-hover table-borderless mb-0 mt-3">
                                <thead class="border-top border-bottom bg-light-subtle border-light">
                                    <tr>
                                        <th>Serviço</th>
                                        <th>Quantidade</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->itens as $i)
                                    <tr>
                                        <td>{{ $i->servico->nome }}</td>
                                        <td>{{ number_format($i->quantidade, 2) }}</td>
                                        <td>{{ __moeda($i->valor) }}</td>

                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td class="text-success">Total</td>
                                        <td class="text-success">R$ {{ __moeda($item->total) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div> <!-- end table-responsive-->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">

                    <!-- end row-->

                    <div class="d-print-none mt-4">

                        @can('agendamento_delete')
                        <form action="{{ route('agendamentos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                            @method('delete')
                            @csrf
                            <button type="button" class="btn btn-delete btn-danger">
                                <i class="ri-delete-bin-line"></i> Remover agendamento
                            </button>
                        </form>
                        @endcan
                        
                        <div class="text-end">

                            <form method="post" action="{{ route('agendamentos.update-status', [$item->id]) }}" id="form-confirm-{{$item->id}}">
                                @method('PUT')
                                @csrf

                                @if($item->nfce_id == null)
                                @can('pdv_create')
                                <a href="{{ route('agendamentos.pdv', [$item->id]) }}" class="btn btn-dark">
                                    <i class="ri-price-tag-3-fill"></i> 
                                    Finalizar no PDV
                                </a>
                                @endcan
                                @endif

                                @if($item->status == 0)
                                @can('agendamento_edit')
                                <button type="button" class="btn btn-success btn-confirm">
                                    <i class="ri-check-line"></i> Alterar para Finalizado
                                </button>
                                @endcan
                                @endif

                                <a href="javascript:window.print()" class="btn btn-primary"><i class="ri-printer-line"></i> Imprimir</a>
                            </form>
                        </div>
                    </div>   
                    <!-- end buttons -->

                </div>
            </div>
        </div>
    </div>
    @endsection