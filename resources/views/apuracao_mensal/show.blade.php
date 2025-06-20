@extends('layouts.app', ['title' => 'Apuração mensal'])
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
                        <h4 class="m-0">{{ $item->funcionario->nome }}</h4>
                    </div>
                </div>

                <!-- Invoice Detail-->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mt-3" style="line-height: 0.7;">


                            <p><b>Mês/Ano: <strong class="text-primary">{{ $item->mes }}/{{ $item->ano }}</strong></b></p>
                            <p><b>Tipo de pagamento: <strong class="text-primary">{{ $item->forma_pagamento }}</strong></b></p>
                        </div>

                    </div><!-- end col -->
                    <div class="col-sm-4 offset-sm-2">

                        <div class="mt-3" style="line-height: 0.7;">


                            <p><b>Data de registro: 
                                <strong class="text-primary">{{ __data_pt($item->created_at) }}</strong></b>
                            </p>

                            @if($item->observacao)
                            <p><b>Observação: 
                                <strong class="text-primary">{{ $item->observacao }}</strong></b>
                            </p>
                            @endif
                            
                        </div>


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
                                        <th>Evento</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->eventos as $i)
                                    <tr>
                                        <td>{{ $i->nome }}</td>
                                        <td>{{ __moeda($i->valor) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-success">Total</td>
                                        <td class="text-success">R$ {{ __moeda($item->valor_final) }}</td>
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

                        <div class="text-end">


                            <a href="javascript:window.print()" class="btn btn-primary"><i class="ri-printer-line"></i> Imprimir</a>

                        </div>
                    </div>   
                    <!-- end buttons -->

                </div>
            </div>
        </div>
    </div>
    @endsection