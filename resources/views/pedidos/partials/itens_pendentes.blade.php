@forelse($data as $item)
<div class="col-lg-4 col-12">
    <div class="card">
        <form method="get" @isset($item->is_cardapio) action="{{ route('pedido-cozinha.update-item', [$item->id])}}" @else action="{{ route('pedidos-delivery.update-item', [$item->id])}}" @endif id="form-{{$item->id}}">
            <div class="card-body" style="height: 320px">

                @isset($item->is_comanda)
                <h4 class="text-center">#{{ $item->id }} <strong class="text-primary">{{ $item->produto->nome }}</strong></h4>
                <h3>Comanda: <strong class="text-danger "> {{ $item->pedido->comanda }}</strong></h3>
                @else
                <h4 class="text-center">#{{ $item->id }} <strong class="text-primary">{{ $item->produto ? $item->produto->nome : '--' }}</strong></h4>
                <h3>Pedido ID: <strong class="text-danger "> {{ $item->pedido->id }}</strong></h3>
                @endif
                <h5>Quantidade: <strong class="text-primary"> {{ number_format($item->quantidade, 2) }}</strong></h5>
                <h5>Subtotal: <strong class="text-primary"> {{ __moeda($item->sub_total) }}</strong></h5>
                <h5>Horário do pedido: <strong class="text-primary"> {{ \Carbon\Carbon::parse($item->created_at)->format('H:i:s') }}</strong></h5>

                <h5>Adicionais: <strong class="text-primary"> {{ sizeof($item->adicionais) > 0 ? $item->getAdicionaisStr() : '--' }}</strong></h5>
                <h5>Observação: <strong class="text-primary"> {{ $item->observacao != '' ? $item->observacao : '--' }}</strong></h5>
                <h5>Estado: 
                    <label class="text-{{ $item->estado }}">{{ strtoupper($item->estado) }}</label>
                </h5>

                @if($item->tempo_preparo > 0)
                <h5>Horário que entrou em preparo: <strong class="text-primary"> {{ \Carbon\Carbon::parse($item->updated_at)->format('H:i:s') }}</strong></h5>
                <h5>Tempo de preparo: <label class="text-info">{{ $item->tempo_preparo }}</label></h5>
                @if($item->tempoPreparoRestante() > -1)
                <h5>Tempo de preparo restante: <label class="text-success">{{ $item->tempoPreparoRestante() }} min</label></h5>
                @else
                <h5>Tempo de atraso para entrega: <label class="text-danger">{{ $item->tempoPreparoRestante()*-1 }} min</label></h5>
                @endif
                @endif

                @if(sizeof($item->pizzas) > 0)
                <h5>Sabores: 
                    @foreach($item->pizzas as $pizza)
                    <label class="text-danger">{{ $pizza->sabor->nome }} @if(!$loop->last) | @endif</label>
                    @endforeach
                </h5>

                @if($item->tamanho)
                <h5>Tamanho: <label class="text-danger">{{ $item->tamanho->nome }}</label></h5>
                @endif
                @endif

                @if($item->ponto_carne)
                <h5>Ponto da carne: <label class="text-danger">{{ $item->ponto_carne }}</label></h5>
                @endif
                @if(isset($item->pedido->_mesa) && $item->pedido->_mesa)
                <h4><label class="text-danger">{{ $item->pedido->_mesa->nome }}</label></h4>
                @endif

                <input type="hidden" name="estado" value="finalizado">
            </div>
            <div class="card-footer mt-2" style="height: 120px">
                @if($item->estado == 'pendente')
                <button type="button" class="btn btn-warning w-100" onclick="openModal()" data-bs-toggle="modal" data-bs-target="#modal-item-{{ $item->id }}">Entrou em preparo</button>
                @endif
                <button type="submit" class="btn btn-success w-100 mt-1">Finalizado</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal-item-{{ $item->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="{{ route('pedido-cozinha.update-item', [$item->id])}}" method="get">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Item <strong class="text-primary">{{ $item->produto ? $item->produto->nome : '--' }}</strong></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="estado" value="preparando">
                        <div class="col-md-12">
                            {!!Form::text('tempo_preparo', 'Tempo de preparo')
                            ->attrs(['data-mask' => '000'])
                            ->value($item->produto ? $item->produto->tempo_preparo : '')
                            !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@empty
<div class="col-12">
    <div class="card">
        <div class="card-body">
            <h4 class="text-center">Nenhum item encontrado!</h4>
        </div>
    </div>
</div>
@endforelse

@if(sizeof($data) > 0)
<div class="row mb-4">
    <div class="col-md-3">
        <a href="{{ route('pedido-cozinha.update-all') }}" class="btn btn-danger">Finalizar todos os itens</a>
    </div>
</div>
@endif


