@extends('layouts.app', ['title' => 'TEF Registros'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('usuario_id', 'Pesquisar por usuário', ['' => 'Selecione'] + $usuarios->pluck('name', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>
                        
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('tef-registros.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Data</th>
                                    <th>Hora</th>
                                    <th>Venda</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                    <th>NSU</th>
                                    <th>Finalização</th>
                                    <th width="15%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Data">{{ $item->getDataTransacao() }}</td>
                                    <td data-label="Hora">{{ $item->getHoraTransacao() }}</td>
                                    <td data-label="Venda">{{ $item->nfce_id }}</td>
                                    <td data-label="Valor">{{ __moeda($item->valor_total/100) }}</td>

                                    <td data-label="Estado">
                                        @if($item->estado == 'aprovado')
                                        <span class="badge bg-success">APROVADO</span>
                                        @elseif($item->estado == 'cancelado')
                                        <span class="badge bg-danger">CANCELADO</span>
                                        @else
                                        <span class="badge bg-warning">PENDENTE</span>
                                        @endif
                                    </td>

                                    <td data-label="NSU">{{ $item->nsu }}</td>
                                    <td data-label="Finalização">{{ $item->finalizacao }}</td>

                                    <td>
                                        <div style="width: 100px;">
                                            @if($item->estado == 'aprovado')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="cancelar('{{ $item->id }}')">
                                                <i class="ri-close-line"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-dark" onclick="imprimir('{{ $item->id }}')">
                                                <i class="ri-printer-line"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}

            </div>
        </div>
    </div>
</div>
@include('modals._tef_consulta')

@endsection

@section('js')
<script type="text/javascript">
    function imprimir(id){
        $.post(path_url + "api/tef/imprimir",
        {
            id: id,
        })
        .done((data) => {
            console.log(data)
            if(data != 'Pendente'){
                window.open('/tef_comprovante/comprovante.pdf')
            }else if(data != 'Pendente'){
                swal("Alerta", "Comprovante pendente aguarde!", "warning")
            }else{
                swal("Erro", "Algo deu errado!", "error")
            }
        })
        .fail((e) => {
            console.log(e)
            swal("Erro", e.responseJSON.erro, "error")
        });
    }

    function cancelar(id){

        swal({
            title: "Você está certo?",
            text: "Você está prestes a estornar o valor da venda!",
            icon: "warning",
            buttons: true,
            buttons: ["Cancelar", "Estornar"],
            dangerMode: true,
        }).then((isConfirm) => {
            if (isConfirm) {

                $.post(path_url + "api/tef/cancelar",
                {
                    id: id,
                })
                .done((data) => {
                    console.log("log cancela", data)
                    consultaCancelamento(data.hash, id)
                })
                .fail((e) => {
                    console.log(e)
                    swal("Erro", e.responseJSON.erro, "error")
                });
            } else {
                swal("", "Este item está salvo!", "info");
            }
        });
    }

    function consultaCancelamento(hash, id){
        $('#modal_tef_consulta').modal('show')
        $('.status-tef').text('Processando')
        $('.loading-tef').removeClass('d-none')

        let data = {
            hash: hash,
            usuario_id: $('#usuario_id').val(),
            empresa_id: $('#empresa_id').val(),
            id: id
        }
        console.log(data)
        $('.modal-loading').remove()
        let intervalo = null;
        intervalo = setInterval(() => {
            // console.log("consultando cancelamento...")
            $.post(path_url + 'api/tef/consulta-cancelamento', data)
            .done((success) => {
                console.log(success)
                if(success == 'Cancelado'){
                    swal("Sucesso", "Operação cancelada!", "success")
                    .then(() => {
                        location.reload()
                        clearInterval(intervalo)
                    })
                }
            })
            .fail((err) => {
                console.log(err)
                clearInterval(intervalo)
                swal("Erro", err.responseJSON, "error")

                $('.status-tef').text(err.responseJSON)
                setTimeout(() => {
                    $('#modal_tef_consulta').modal('hide')
                }, 2000)
            })
        }, 10000)
    }
</script>
@endsection