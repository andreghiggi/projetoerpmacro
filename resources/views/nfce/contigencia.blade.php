@extends('layouts.app', ['title' => 'Arquivos XML NFCe'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Arquivos XML NFCe</h4>
        
    </div>
    <div class="card-body">
        <hr class="mt-3">
        <div class="col-lg-12">


            <div class="col-md-12 mt-3">
                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Cliente</th>
                                <th>Número</th>
                                <th>Chave</th>
                                <th>Valor</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td data-label="Cliente">{{ $item->cliente ? $item->cliente->info : '--' }}</td>
                                <td data-label="Número">{{ $item->numero }}</td>
                                <td data-label="Chave">{{ $item->chave }}</td>
                                <td data-label="Valor">{{ __moeda($item->total) }}</td>
                                <td data-label="Data">{{ __data_pt($item->created_at) }}</td>
                                <td>
                                    <div style="width: 100px;"> 
                                        @if($item->reenvio_contigencia == 0 && $item->contigencia)
                                        <button title="Transmitir NFe" type="button" class="btn btn-success btn-sm" onclick="transmitirContigencia('{{$item->id}}')">
                                            <i class="ri-send-plane-fill"></i>
                                        </button>
                                        @endif
                                        <a target="_blank" title="XML temporário" class="btn btn-light btn-sm" href="{{ route('nfce.xml-temp-contigencia', $item->id) }}">
                                            <i class="ri-file-line"></i>
                                        </a>
                                    </div>
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

@endsection
@section('js')
<script type="text/javascript" src="/js/nfce_transmitir.js"></script>
@endsection

