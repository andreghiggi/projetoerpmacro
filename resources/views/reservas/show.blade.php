@extends('layouts.app', ['title' => 'Visualizando Reserva'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Visualizando Reserva <strong class="text-success">#{{ $item->numero_sequencial }}</strong></h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('reservas.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <input type="hidden" id="reserva_id" value="{{ $item->id }}">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-6">
                <span class="badge bg-{{ $item->colorStatus() }}">{{ strtoupper($item->estado) }}</span>
            </div>

            @if($item->hospedes)
            <div class="col-md-6">
                @if($item->estado == 'iniciado')
                <button type="button" class="btn btn-dark btn-sm" id="btn-hospedes">
                    <i class="ri-folder-user-fill"></i>
                    Hóspedes
                </button>
                @endif

                <a target="_blank" href="{{ route('reservas.imprimir', [$item->id]) }}" type="button" class="btn btn-primary btn-sm" id="btn-hospedes">
                    <i class="ri-printer-fill"></i>
                    Imprimir
                </a>

                @if($item->estado == 'iniciado')
                <button type="button" class="btn btn-success btn-sm" id="btn-fatura">
                    <i class="ri-wallet-line"></i>
                    Fatura
                </button>

                @if(sizeof($item->fatura) > 0)
                <a class="btn btn-light btn-sm" href="{{ route('conta-receber.index', ['reserva_id='.$item->id]) }}">Contas a receber da reserva</a>
                @endif
                @endif
            </div>
            @endif
        </div>
        <div class="row">

            <div class="col-md-6">
                <h5>Cliente: <strong class="text-primary">{{ $item->cliente->info }}</strong></h5>
                <h5>Data de criação da reserva: <strong class="text-primary">{{ __data_pt($item->created_at) }}</strong></h5>
                <h5>Valor da estádia: <strong class="text-primary">{{ __moeda($item->valor_estadia) }}</strong></h5>
                <h5>Valor total: <strong class="text-success">{{ __moeda($item->valor_total) }}</strong></h5>
            </div>
            <div class="col-md-6">
                <h5>Acomodação: <strong class="text-primary">{{ $item->acomodacao->info }}</strong></h5>
                <h5>Total de hóspedes: <strong class="text-primary">{{ $item->total_hospedes }}</strong></h5>
                <h5>Checkin/checkout: <strong class="text-primary">{{ __data_pt($item->data_checkin, 0) }} à {{ __data_pt($item->data_checkout, 0) }}</strong></h5>

                @if($item->estado == 'iniciado' || $item->estado == 'pendente')
                <h5>Link do cliente <a target="_blank" href="{{ $item->link_externo }}">{{ $item->link_externo }}</a></h5>
                @endif
            </div>

            @if($item->estado == 'iniciado')
            <div class="col-md-12">
                <form action="{{ route('reservas.update-estado', $item->id) }}" method="post" id="form-{{$item->id}}">
                    @method('put')
                    @csrf
                    <input type="hidden" name="estado" value="finalizado">
                    <button type="button" class="btn btn-dark btn-sm btn-update-estado">
                        <i class="ri-checkbox-circle-line"></i>
                        Alterar para finalizado
                    </button>

                    @if($item->conferencia_frigobar == 0)
                    <a href="{{ route('reservas.conferir-frigobar', [$item->id]) }}" type="button" class="btn btn-light btn-sm" id="btn-hospedes">
                        <i class="ri-door-fill"></i>
                        Conferir frigobar
                    </a>
                    @else
                    <h5 class="text-success mt-2"><i class="ri-checkbox-circle-line"></i> Frigobar conferido!</h5>
                    @endif
                </form>


            </div>
            @endif

            @if($item->estado == 'pendente')
            @can('reserva_delete')
            <div class="col-md-12">
                <form action="{{ route('reservas.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                    @method('delete')
                    @csrf
                    <button type="button" class="btn btn-danger btn-sm btn-delete">
                        <i class="ri-delete-bin-line"></i>
                        Remover reserva
                    </button>
                </form>
            </div>
            @endcan
            @endif
            <div class="col-md-12 text-end">

                @if($item->estado == 'pendente')
                <a href="{{ route('reservas.checkin', [$item->id]) }}" class="btn btn-success btn-sm">
                    <i class="ri-check-fill"></i>
                    Iniciar checkin
                </a>
                @endif

                @if($item->estado == 'pendente' || $item->estado == 'iniciado')
                <button type="button" class="btn btn-danger btn-sm" id="btn-cancelar">
                    <i class="ri-close-fill"></i>
                    Cancelar reserva
                </button>
                @endif

            </div>
        </div>

        @if($item->estado == 'iniciado')
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Consumo</h4>
                    </div>
                    <div class="card-body">
                        <form class="row g-2" method="post" action="{{ route('reservas.store-produto', [$item->id]) }}">
                            @csrf
                            <div class="col-md-4">
                                {!!Form::select('produto_id', 'Produto')->required()
                                !!}
                            </div>

                            <div class="col-md-2 col">
                                {!!Form::tel('quantidade_produto', 'Quantidade')
                                ->attrs(['class' => 'qtd'])
                                ->required()
                                !!}
                            </div>

                            <div class="col-md-2 col">
                                {!!Form::tel('valor_unitario_produto', 'Valor unitário')
                                ->attrs(['class' => 'moeda'])
                                ->required()
                                !!}
                            </div>
                            <div class="col-md-2 col">
                                {!!Form::tel('sub_total_produto', 'Subtotal')
                                ->attrs(['class' => 'moeda'])
                                ->required()
                                !!}
                            </div>

                            <div class="col-md-2">
                                {!!Form::select('frigobar', 'Frigobar', ['0' => 'Não', '1' => 'Sim'])
                                ->attrs(['class' => 'form-select'])
                                ->required()
                                !!}
                            </div>

                            <div class="col-md-6">
                                {!!Form::text('observacao', 'Observação do item')
                                !!}
                            </div>

                            <div class="col-md-2">
                                <br>
                                <button class="btn btn-dark">
                                    <i class="ri-add-circle-fill"></i>
                                    Adicionar
                                </button>
                            </div>
                        </form>
                        <div class="table-responsive mt-2">
                            <table class="table" id="table-produtos">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Valor unitário</th>
                                        <th>Subtotal</th>
                                        <th>Frigobar</th>
                                        <th>Observação</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->consumoProdutos as $p)
                                    <tr>
                                        <td>{{ $p->produto->nome }}</td>
                                        <td>{{ $p->quantidade }}</td>
                                        <td>{{ __moeda($p->valor_unitario) }}</td>
                                        <td>{{ __moeda($p->sub_total) }}</td>
                                        <td>
                                            @if($item->frigobar)
                                            <i class="ri-checkbox-circle-fill text-success"></i>
                                            @else
                                            <i class="ri-close-circle-fill text-danger"></i>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $p->observacao }}
                                        </td>
                                        <td>
                                            <form action="{{ route('reservas.destroy-produto', $p->id) }}" method="post" id="form-{{$p->id}}">
                                                @method('delete')
                                                @csrf

                                                <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(sizeof($item->consumoProdutos) > 0)
                        @if($item->nfe_id == 0)
                        <a class="btn btn-success btn-sm d-print-none" href="{{ route('reservas.gerar-nfe', $item->id) }}">
                            <i class="ri-file-text-line"></i>
                            Gerar NFe de consumo
                        </a>
                        @else
                        <a class="btn btn-success btn-sm d-print-none" href="{{ route('nfe.show', $item->nfe_id) }}">
                            <i class="ri-file-text-line"></i>
                            Ver NFe
                        </a>
                        @endif
                        @endif
                        <h5 class="text-end">Total de produtos <strong>R$ {{ __moeda($item->consumoProdutos->sum('sub_total') )}}</strong></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Serviços</h4>
                    </div>
                    <div class="card-body">
                        <form class="row g-2" method="post" action="{{ route('reservas.store-servico', [$item->id]) }}">
                            @csrf
                            <div class="col-md-4">
                                {!!Form::select('servico_id', 'Serviço')->required()
                                !!}
                            </div>

                            <div class="col-md-2 col">
                                {!!Form::tel('quantidade_servico', 'Quantidade')
                                ->attrs(['class' => 'qtd'])
                                ->required()
                                !!}
                            </div>

                            <div class="col-md-2 col">
                                {!!Form::tel('valor_unitario_servico', 'Valor unitário')
                                ->attrs(['class' => 'moeda'])
                                ->required()
                                !!}
                            </div>
                            <div class="col-md-2 col">
                                {!!Form::tel('sub_total_servico', 'Subtotal')
                                ->attrs(['class' => 'moeda'])
                                ->required()
                                !!}
                            </div>

                            <div class="col-md-6">
                                {!!Form::text('observacao', 'Observação do serviço')
                                !!}
                            </div>

                            <div class="col-md-2">
                                <br>
                                <button class="btn btn-dark">
                                    <i class="ri-add-circle-fill"></i>
                                    Adicionar
                                </button>
                            </div>
                        </form>
                        <div class="table-responsive mt-2">
                            <table class="table" id="table-servicos">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Serviço</th>
                                        <th>Quantidade</th>
                                        <th>Valor unitário</th>
                                        <th>Subtotal</th>
                                        <th>Observação</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->consumoServicos as $p)
                                    <tr>
                                        <td>{{ $p->servico->nome }}</td>
                                        <td>{{ $p->quantidade }}</td>
                                        <td>{{ __moeda($p->valor_unitario) }}</td>
                                        <td>{{ __moeda($p->sub_total) }}</td>
                                        <td>
                                            {{ $p->observacao }}
                                        </td>
                                        <td>
                                            <form action="{{ route('reservas.destroy-servico', $p->id) }}" method="post" id="form-{{$p->id}}">
                                                @method('delete')
                                                @csrf

                                                <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>


                        @if($item->nfse_id == 0)
                        @can('nfse_create')
                        <a class="btn btn-success btn-sm d-print-none" href="{{ route('reservas.gerar-nfse', $item->id) }}">
                            <i class="ri-file-text-line"></i>
                            Gerar NFSe
                        </a>
                        @endcan
                        @else
                        <a class="btn btn-success btn-sm d-print-none" href="{{ route('nota-servico.show', $item->nfse_id) }}">
                            <i class="ri-file-text-line"></i>
                            Ver NFSe
                        </a>
                        @endif

                        <h5 class="text-end">Total de serviços <strong>R$ {{ __moeda($item->consumoServicos->sum('sub_total') )}}</strong></h5>

                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Notas da reserva</h4>
                    </div>
                    <div class="card-body">
                        <form class="row g-2" method="post" action="{{ route('reservas.store-nota', [$item->id]) }}">
                            @csrf
                            <div class="col-md-12">
                                {!!Form::textarea('texto', 'Texto')->required()
                                !!}
                            </div>

                            <div class="col-md-12 text-end">
                                <br>
                                <button class="btn btn-dark">
                                    <i class="ri-add-circle-fill"></i>
                                    Adicionar
                                </button>
                            </div>
                        </form>
                        <div class="table-responsive mt-2">
                            <table class="table" id="table-notas">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 90%;">Texto</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item->notas as $p)
                                    <tr>

                                        <td style="width: 90%;">
                                            {{ $p->texto }}
                                        </td>
                                        <td>
                                            <form action="{{ route('reservas.destroy-nota', $p->id) }}" method="post" id="form-{{$p->id}}">
                                                @method('delete')
                                                @csrf

                                                <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
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
        @endif
    </div>
</div>

@include('modals._cancelamento_reserva')
@include('modals._hospedes')
@include('modals._fatura_reserva')

@endsection

@section('js')
<script type="text/javascript" src="/js/reserva.js"></script>
<script type="text/javascript">
    $('#btn-cancelar').click(() => {
        $('#modal_cancelamento_reserva').modal('show')
    })
    $('#btn-fatura').click(() => {
        $('#modal_fatura').modal('show')
    })

    $('body').on('blur', '.valor_fatura', function () {
        var total = 0
        $(".valor_fatura").each(function () {
            total += convertMoedaToFloat($(this).val())
        })

        setTimeout(() => {
            $('#total_fatura').text("R$ " + convertFloatToMoeda(total))
        }, 20)
    })

    
    $(".btn-update-estado").on("click", function (e) {
        e.preventDefault();
        var form = $(this).parents("form").attr("id");

        swal({
            title: "Você está certo?",
            text: "Uma vez alterado, você não poderá voltar esse estado novamente!",
            icon: "warning",
            buttons: true,
            buttons: ["Cancelar", "Alterar"],
            dangerMode: true,
        }).then((isConfirm) => {
            if (isConfirm) {

                document.getElementById(form).submit();
            } else {
                swal("", "Essa reserva não foi alterada!", "info");
            }
        });
    });
</script>
@endsection


