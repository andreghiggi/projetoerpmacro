@extends('layouts.app', ['title' => 'Lista de Caixa'])
@section('content')

<div class="card mt-1">
    <div class="card-body">
        @if(__isAdmin())
        <a href="{{ route('caixa.abertos-empresa') }}" class="btn btn-dark mb-2">
            <i class="ri-list-indefinite"></i>
            Listar todos os caixas abertos
        </a>
        @endif
        <div class="table-responsive">
            <table class="table table-striped table-centered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th data-label="Caixa">Caixa</th>
                        <th data-label="Data Abertura">Data Abertura</th>
                        <th data-label="Data Fechamento">Data Fechamento</th>
                        <th data-label="Valor Abertura">Valor Abertura</th>
                        <th data-label="Valor Fechamento">Valor Fechamento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                    <tr>
                        <td data-label="Caixa">{{ $item->usuario ? $item->usuario->name : '--' }}</td>
                        <td data-label="Data Abertura">{{ __data_pt($item->created_at) }}</td>
                        <td data-label="Data Fechamento">{{ $item->data_fechamento ? __data_pt($item->data_fechamento) : '--' }}</td>
                        <td data-label="Valor Abertura">{{ __moeda($item->valor_abertura) }}</td>
                        <td data-label="Valor Fechamento">{{ __moeda($item->valor_fechamento) }}</td>
                        <td>
                            <div style="width: 100px;"> 
                                @if($item->status == 0)
                                <button type="button" onclick="imprimir('{{$item->id}}')" class="btn btn-dark btn-sm" title="Imprimir">
                                    <i class="ri-printer-line"></i>
                                </button>
                                @endif
                                <a class="btn btn-primary btn-sm" href="{{ route('caixa.show' , $item) }}">
                                    <i class="ri-list-indefinite"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Nada encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-print" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Imprimir Relatório</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <button type="button" class="btn btn-success w-100" onclick="print('a4')">
                            <i class="ri-printer-line"></i>
                            Modelo A4
                        </button>
                    </div>

                    <div class="col-12 col-lg-6">
                        <button type="button" class="btn btn-primary w-100" onclick="print('80')">
                            <i class="ri-printer-line"></i>
                            80mm
                        </button>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    var ID = 0
    function imprimir(id){
        ID = id
        $('#modal-print').modal('show')
    }

    function print(tipo){
        if(tipo == 'a4'){
            window.open('/caixa/imprimir/'+ID)
        }else{
            window.open('/caixa/imprimir80/'+ID)
        }
        $('#modal-print').modal('hide')
    }
</script>
@endsection
