@extends('layouts.app', ['title' => 'Lista de Trocas'])

@section('css')
<style type="text/css">
    #scroll-trocas thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #343a40; /* mesma cor da table-dark */
        color: #fff;
    }
</style>
@endsection
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        @can('troca_create')
                        <button class="btn btn-success btn-nova-troca" data-bs-toggle="modal" data-bs-target="#modal-nova-troca">
                            <i class="ri-add-circle-fill"></i>
                            Nova Troca/Devolução
                        </button>
                        @endcan
                    </div>

                </div>
                
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-4">
                            {!!Form::select('cliente_id', 'Cliente')
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final')
                            !!}
                        </div>
                        

                        <div class="col-md-4">
                            <br>

                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('trocas.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-lg-12 mt-4">
                    <div id="tabela-trocas" class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Código</th>
                                    <th>Valor da troca</th>
                                    <th>Valor da venda</th>
                                    <th>Data da troca</th>
                                    <th>Venda ID</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="#">{{ $item->numero_sequencial }}</td>

                                    <td data-label="Cliente">
                                        <label style="width: 400px;">
                                            @if($item->nfce)
                                            {{ $item->nfce->cliente ? $item->nfce->cliente->razao_social : "--" }}
                                            @else
                                            {{ $item->nfe && $item->nfe->cliente ? $item->nfe->cliente->razao_social : "--" }}
                                            @endif
                                        </label>
                                    </td>

                                    <td data-label="Código">{{ $item->codigo }}</td>
                                    <td data-label="Valor da troca">{{ number_format($item->valor_troca, 2, ',', '.') }}</td>
                                    <td data-label="Valor da venda">{{ number_format($item->valor_original, 2, ',', '.') }}</td>

                                    <td data-label="Data da troca">
                                        <label style="width: 120px">{{ __data_pt($item->created_at) }}</label>
                                    </td>

                                    <td data-label="Venda ID">
                                        {{ $item->nfce ? $item->nfce->numero_sequencial : $item->nfe->numero_sequencial }}
                                    </td>

                                    <td>
                                        <form action="{{ route('trocas.destroy', $item->id) }}" method="post" id="form-{{$item->id}}" style="width: 320px">
                                            @method('delete')
                                            @csrf

                                            @can('troca_delete')
                                            <button type="button" class="btn btn-danger btn-sm btn-delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan

                                            <a class="btn btn-ligth btn-sm" title="Detalhes" href="{{ route('trocas.show', $item->id) }}">
                                                <i class="ri-eye-line"></i>
                                            </a>


                                            <a title="Imprimir" onclick="imprimir('{{$item->id}}')" class="btn btn-primary btn-sm">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                        </form>
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
                <br>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-troca" id="modal-nova-troca" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form method="get" action="{{ route('trocas.create') }}">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nova Troca/Devolução</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-2">
                            {!!Form::date('start_date_aux', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date_aux', 'Data final')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::tel('codigo_venda', 'Código da venda')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::tel('numero_documento', 'Número NFe/NFCe')
                            !!}
                        </div>

                        <div class="col-md-4">
                            <br>
                            <button id="btn-pesquisar-troca" class="btn btn-primary" type="button">
                                <i class="ri-search-line"></i> Pesquisar
                            </button>
                        </div>
                    </div>

                    <div id="scroll-trocas" class="table-responsive mt-2" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Valor</th>
                                    <th>Data</th>
                                    <th>Nº NFe/NFCe</th>
                                    <th>Estado</th>
                                    <th>Tipo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </form>
    </div>
</div>

@section('js')
<script type="text/javascript">

    let page = 1;
    let loading = false;
    let lastPage = false;

    $('#btn-pesquisar-troca').on('click', function () {

        page = 1;
        lastPage = false;

        $('#scroll-trocas tbody').html('');

        carregarLinhas();
    });

    function carregarLinhas() {

        if (loading || lastPage) return;
        loading = true;

        $.get(path_url + "api/vendas/filtro-troca", {
            empresa_id: $('#empresa_id').val(),
            start_date: $('#inp-start_date_aux').val(),
            end_date: $('#inp-end_date_aux').val(),
            codigo_venda: $('#inp-codigo_venda').val(),
            numero_documento: $('#inp-numero_documento').val(),
            page: page
        })
        .done((res) => {

            $('#scroll-trocas tbody').append(res.html);
            if (res.lastPage) {
                lastPage = true;
            } else {
                page++;
            }

            loading = false;
        })
        .fail((err) => {
            console.log("ERRO AJAX", err);
            loading = false;
        });
    }


    $('#scroll-trocas').on('scroll', function () {

        let el = $(this);

        if (el.scrollTop() + el.innerHeight() >= el[0].scrollHeight - 10) {
            carregarLinhas();
        }

    });


    $(document).on("click", ".btn-nova-troca", function () {
        page = 1;
        lastPage = false;
        $('#scroll-trocas tbody').html('');
        carregarLinhas();
    })


    function imprimir(id){
        var disp_setting="toolbar=yes,location=no,";
        disp_setting+="directories=yes,menubar=yes,";
        disp_setting+="scrollbars=yes,width=850, height=600, left=100, top=25";

        var docprint=window.open(path_url+"trocas/imprimir/"+id, "",disp_setting);

        docprint.focus();
    }

</script>
@endsection
@endsection


