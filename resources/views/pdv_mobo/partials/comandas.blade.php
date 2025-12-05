<div class="mt-2">

    @if($data->isEmpty())
    <p class="text-muted">Nenhuma comanda aberta no momento.</p>
    @else
    <input type="text" class="form-control mb-2" id="filtrarComandas" placeholder="Buscar comanda">
    <h5 class="mb-1">Comandas abertas</h5>
    <div class="grid-comandas">

        @foreach($data as $c)
        <a href="{{ route('pdv-mobo.index', ['pedido_id='.$c->id]) }}">
            <div class="mesa-box mesa-livre">

                <div class="mesa-numero">
                    {{ str_pad($c->comanda, 3, '0', STR_PAD_LEFT) }}
                </div>

                <div class="mesa-total">
                    R$ {{ __moeda($c->total) }}
                </div>

                @if($c->_mesa)
                <div class="mesa">
                    {{ $c->_mesa->nome }}
                </div>
                @endif

            </div>
        </a>
        @endforeach
    </div>
    <h5 class="mb-1 mt-2">Comandas fechadas</h5>
    <div class="grid-comandas">
        @foreach($comandasFechadas as $c)
        <a href="{{ route('pdv-mobo.index', ['comanda='.$c['numero']]) }}">
            <div class="mesa-box mesa-fechada">

                <div class="mesa-numero">
                    {{ str_pad($c['numero'], 3, '0', STR_PAD_LEFT) }}
                </div>

                <div class="mesa-total">
                    R$ {{ __moeda($c['total']) }}
                </div>

            </div>
        </a>
        @endforeach
    </div>
    @endif

    @if($comandasConfiguradas == 0)
    <p class="text-danger mt-3 fw">Configure a numeração das comandas em configuração geral para poder abrir novas comandas!</p>
    @endif
</div>