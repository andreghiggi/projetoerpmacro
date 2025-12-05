@extends('layouts.app', ['title' => 'Minhas Faturas'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <hr class="mt-3">

                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Vencimento</th>
                                    <th>Valor Integral</th>
                                    <th>Valor Pago</th>
                                    <th>Status</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{ __data_pt($item->vencimento, 0) }}</td>
                                    <td>{{ __moeda($item->valor) }}</td>
                                    <td>{{ __moeda($item->valor_recebido) }}</td>
                                    <td>
                                        @if($item->status)
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <a target="_blank" title="Imprimir boleto" class="btn btn-dark btn-sm" href="{{ $item->pdf_boleto }}">
                                            <i class="ri-printer-line"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection