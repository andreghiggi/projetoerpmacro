@extends('layouts.app', ['title' => 'Pedidos (Comandas)'])
@section('css')
<style type="text/css">
    .card-title strong{
        color: #159488;
    }

    h4 strong{
        color: #4254BA;
    }
</style>
@endsection
@section('content')

<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <hr>
                <button class="btn btn-success px-3" type="button" data-bs-toggle="modal" data-bs-target="#modal-comanda">
                    <i class="ri-add-circle-fill"></i>
                    Abrir comanda
                </button>
                <div class="row mt-3">
                    @foreach($data as $item)
                    <a class="col-12 col-lg-3" href="{{ route('pedidos-cardapio.show', [$item->id]) }}">
                        <div class="card">

                            <div class="card-body" style="height: 180px">
                                <h3 class="card-title">Comanda: <strong>{{ $item->comanda }}</strong></h3>

                                <h4>Total: <strong>{{ __moeda($item->total) }}</strong></h4>
                                <h4>Cliente: <strong>{{ $item->cliente_nome != "" ? $item->cliente_nome : 'não identificado' }}</strong></h4>
                                <h4>Mesa: <strong>{{ $item->mesa ? $item->mesa : '--' }}</strong></h4>

                                @if(!$item->em_atendimento)
                                <span class="text-danger">Pedindo para fechar</span>
                                @endif

                            </div>
                            @if(__isAdmin() || sizeof($item->itens) == 0)
                            <div class="card-footer">
                                <form action="{{ route('pedidos-cardapio.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                    @method('delete')
                                    @csrf
                                    <button class="btn btn-danger btn-delete w-100">
                                        Remover comanda
                                    </button>
                                </form>
                                @endif
                                
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-comanda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('pedidos-cardapio.store') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Abertura de Comanda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-2">
                            {!!Form::text('comanda', 'Número comanda')
                            ->required()
                            ->attrs(['data-mask' => 'AAAAAAAA'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::tel('mesa', 'Mesa')
                            !!}
                        </div>

                        <div class="col-md-4">
                            {!!Form::select('cliente_id', 'Cliente')->attrs(['class' => 'select2'])
                            !!}
                        </div>

                        <div class="col-md-3">
                            {!!Form::text('cliente_nome', 'Cliente nome')
                            !!}
                        </div>

                        <div class="col-md-3">
                            {!!Form::text('cliente_fone', 'Cliente telefone')
                            ->attrs(['class' => 'fone'])
                            !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success">Abrir</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    $(function(){

        setTimeout(() => {
            $('.modal .select2').each(function () {
                $(this).select2({
                    minimumInputLength: 2,
                    dropdownParent: $(this).parent(),
                    language: "pt-BR",
                    placeholder: "Digite para buscar o cliente",
                    theme: "bootstrap4",

                    ajax: {
                        cache: true,
                        url: path_url + "api/clientes/pesquisa",
                        dataType: "json",
                        data: function (params) {
                            console.clear();
                            var query = {
                                pesquisa: params.term,
                                empresa_id: $("#empresa_id").val(),
                            };
                            return query;
                        },
                        processResults: function (response) {
                            var results = [];

                            $.each(response, function (i, v) {
                                var o = {};
                                o.id = v.id;

                                o.text = v.razao_social + " - " + v.cpf_cnpj;
                                o.value = v.id;
                                results.push(o);
                            });
                            return {
                                results: results,
                            };
                        },
                    },
                });
            });
        }, 10)
    })

    $('body').on('change', '#inp-cliente_id', function () {
        let id = $(this).val()
        $.get(path_url + 'api/clientes/find/'+id)
        .done((success) => {
            $('#inp-cliente_nome').val(success.razao_social)
            $('#inp-cliente_fone').val(success.telefone)
        })
        .fail((err) => {
            console.log(err)
        })
    });
</script>
@endsection

