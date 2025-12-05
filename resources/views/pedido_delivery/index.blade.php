@extends('layouts.app', ['title' => 'Pedidos de Delivery'])
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

<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <hr>
                <button class="btn btn-success px-3" type="button" data-bs-toggle="modal" data-bs-target="#modal-comanda">
                    <i class="ri-add-circle-fill"></i>
                    Novo Pedido de Delivery
                </button>

                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('cliente_delivery_id', 'Pesquisar por cliente')
                            ->options($cliente != null ? [$cliente->id => ($cliente->razao_social . " - " . $cliente->telefone)] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('estado', 'Estado', ['' => 'Selecione'] + App\Models\PedidoDelivery::estados())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        @if(__isProdutoServicoDelivery(request()->empresa_id))
                        <div class="col-md-2">
                            {!!Form::select('tipo', 'Tipo', ['' => 'Todos', 'delivery' => 'Somente Delivery', 'agendamento' => 'Somente Agendamento'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        @endif
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('pedidos-delivery.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="row mt-3">
                    @foreach($data as $item)
                    <a class="col-12 col-lg-4" href="{{ route('pedidos-delivery.show', [$item->id]) }}">
                        <div class="card">

                            <div class="card-body" style="height: 305px">
                                <h3 class="card-title">ID: <strong>#{{ $item->numero_sequencial }}</strong></h3>

                                <h4>Total: <strong>R$ {{ __moeda($item->valor_total) }}</strong></h4>
                                <h4>Cliente: <strong>{{ $item->cliente->razao_social }}</strong></h4>
                                <h4>Data: <strong>{{ __data_pt($item->created_at) }}</strong></h4>
                                <h4>Total de itens: <strong>{{ sizeof($item->itens) }}</strong></h4>
                                @if($item->endereco)
                                <h4>Endereço: <strong class="text-primary">{{ $item->endereco->info }}</strong></h4>
                                @else
                                <h4 class="text-primary">Retirada no balcão</h4>
                                @endif

                                Estado do pedido {!! $item->_estado() !!}<br>
                                Estado do pagamento {!! $item->_estadoPagamento() !!}
                                @if($item->inicio_agendamento)
                                <h5 class="text-danger">Pedido com agendamento</h5>
                                @endif

                                @if($item->estado == 'novo')
                                <form method="post" action="{{ route('pedidos-delivery.update', [$item->id]) }}" class="row">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="estado" value="aprovado">
                                    <div class="col-12">
                                        <button class="btn btn-success btn-sm">
                                            Aprovar Pedido
                                            <i class="ri-checkbox-circle-fill"></i>
                                        </button>
                                    </div>
                                </form>
                                @endif

                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                {!! $data->appends(request()->all())->links() !!}
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-comanda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('pedidos-delivery.store') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Abertura de Pedido de Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-4">
                            {!!Form::select('cliente_id', 'Cliente')->attrs(['class' => 'select2'])
                            !!}
                        </div>

                        <div class="col-md-3">
                            {!!Form::text('cliente_nome', 'Cliente nome')->required()
                            !!}
                        </div>

                        <div class="col-md-3">
                            {!!Form::text('cliente_fone', 'Cliente telefone')->required()
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

