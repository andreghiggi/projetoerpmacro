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
                        <th>Caixa</th>
                        <th>Data Abertura</th>
                        <th>Data Fechamento</th>
                        <th>Valor Abertura</th>
                        <th>Valor Fechamento</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                    <tr>
                        <td>{{ $item->usuario ? $item->usuario->name : '--' }}</td>
                        <td>{{ __data_pt($item->created_at) }}</td>
                        <td>{{ $item->data_fechamento ? __data_pt($item->data_fechamento) : '--' }}</td>
                        <td>{{ __moeda($item->valor_abertura) }}</td>
                        <td>{{ __moeda($item->valor_fechamento) }}</td>
                        <td>
                            @if($item->status == 0)
                            <!-- <a target="_blank" class="btn btn-dark btn-sm" href="{{ route('caixa.imprimir' , $item) }}"><i class="ri-printer-fill"></i></a> -->
                            <button type="button" onclick="imprimir('{{$item->id}}')" class="btn btn-dark btn-sm" title="Imprimir">
                                <i class="ri-printer-line"></i>
                            </button>
                            @endif

                            <a class="btn btn-primary btn-sm" href="{{ route('caixa.show' , $item) }}"><i class=" ri-list-indefinite"></i></a>
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
