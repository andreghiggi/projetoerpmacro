
<div id="loaderNfce" class="loader" style="display: none">
    <div class="loader-spinner"></div>
    <div class="loader-text">Emitindo NFCe...</div>
</div>

<div class="pdv-header d-flex align-items-center">
    <button class="btn btn-link p-0 me-3" id="btn-menu">
        <i class="ri-menu-line"></i>
    </button>
    @if($modelo == 'pedido')
    <h4>{{ env("APP_NAME") }} PDV</h4>
    @else
    <h4>{{ env("APP_NAME") }} PDV</h4>
    @endif
    @if($isComanda == 1)
    <span class="comanda-tag">Comanda {{ $item->comanda }}</span>
    @endif
    @if($isVendaSuspensa == 1)
    <span class="comanda-tag">Finalizando suspensa</span>
    @endif
</div>

<div class="px-1">
    <div class="pdv-categorias">
        <button class="pdv-cat active" data-cat="0">Todos</button>
        @foreach($categorias as $c)
        <button class="pdv-cat" data-cat="{{ $c->id }}">{{ $c->nome }}</button>
        @endforeach
    </div>

    <!-- <input type="text" id="buscarProduto" class="form-control pdv-search" placeholder="Buscar produto..." autocomplete="off"> -->
    <div class="pdv-search-wrapper">
        <input type="text" id="buscarProduto" class="form-control pdv-search" placeholder="Buscar produto..." autocomplete="off">
        <button id="btnScan" class="pdv-search-btn">
            <i class="ri-barcode-box-line"></i>
        </button>
    </div>

    <!-- <div id="reader" style="width:100%; height:280px; display:none; border-radius:10px; overflow:hidden; position:relative; margin-top:10px;">
        <button id="btnCloseScanner" class="btn-close-scanner">x</button>
    </div> -->

    <div id="reader" style="width:100%; height:280px; display:none; border-radius:10px; overflow:hidden; position:relative; margin-top:10px;">
    </div>

    <button id="btnCloseScanner" class="btn-close-scanner d-none">✖</button>


</div>

<div class="pdv-produtos-container">
    <div class="row g-1">
        @foreach($produtos as $p)
        <div class="col-4 produto-item pdv-animar" data-cat="{{ $p->categoria_id }}">
            <div class="pdv-card" data-produto='@json($p)'>
                <img src="{{ $p->img ?? '/noimg.png' }}">
                <div class="pdv-card-title">{{ $p->nome }}</div>
                <div class="pdv-card-price">R$ {{ __moeda($p->valor_unitario) }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<button class="pdv-cart-btn" id="btnCarrinho" style="display:none;">
    <i class="ri-shopping-cart-fill"></i><span id="cartCount">0</span>
</button>

@if($isComanda == 1)
<button id="btnSalvarComanda" class="btn-salvar-comanda">
    <i class="ri-smartphone-line"></i> Salvar Comanda
</button>
@endif

<div class="pdv-footer">
    <div class="d-flex align-items-center">
        <i class="ri-user-line fs-4 me-2"></i>
        <div class="escolherCliente">
            <strong id="clienteNome">Cliente não selecionado</strong><br>
            <small id="limparCliente" class="text-muted" style="cursor:pointer">Selecionar</small>
        </div>
    </div>

    <button class="pdv-total-btn" id="btnTotal">R$ 0,00</button>
</div>

<div id="pdv-sidebar" class="pdv-sidebar">

    <div class="pdv-sidebar-header">
        <div class="logo-box">
            <img src="/logo.png" class="logo-sidebar">
        </div>
        <button id="close-sidebar" class="close-btn">
            <i class="ri-close-line"></i>
        </button>
    </div>

    <div class="pdv-sidebar-body">

        <button class="pdv-side-item" id="btn-sangria">
            <i class="ri-indeterminate-circle-fill"></i>
            Sangria de Caixa
        </button>

        <button class="pdv-side-item" id="btn-suprimento">
            <i class="ri-add-circle-fill"></i>
            Suprimento de Caixa
        </button>

        @if($modelo == 'pedido')

        <a class="pdv-side-item text-success" href="{{ route('pdv-mobo.index', ['modelo=pedido']) }}">
            <i class="ri-shopping-basket-fill text-success"></i>
            Nova venda
        </a>

        <a class="pdv-side-item text-danger" href="{{ route('nfe.index') }}">
            <i class="ri-arrow-left-double-fill text-danger"></i>
            Sair da Venda
        </a>
        @else

        <button class="pdv-side-item" id="btn-vendas-diaria">
            <i class="ri-list-ordered"></i>
            Vendas do dia
        </button>

        <button class="pdv-side-item" id="btn-comandas">
            <i class="ri-file-list-3-line"></i>
            Comandas
        </button>

        @if($isComanda == 0 && $isVendaSuspensa == 0)
        
        <button class="pdv-side-item" id="btn-reiniciar">
            <i class="ri-refresh-line"></i>
            Reiniciar Venda
        </button>

        <button class="pdv-side-item" id="btn-suspender">
            <i class="ri-close-circle-fill"></i>
            Suspender Venda
        </button>
        @else
        <a class="pdv-side-item text-success" href="{{ route('pdv-mobo.index') }}">
            <i class="ri-shopping-basket-fill text-success"></i>
            Nova venda
        </a>
        @endif

        <button class="pdv-side-item" id="btn-vendas-suspensas">
            <i class="ri-list-ordered"></i>
            Vendas Suspensa
        </button>

        <div class="pdv-menu-sep"></div>

        <a class="pdv-side-item text-danger" href="{{ route('frontbox.index') }}">
            <i class="ri-arrow-left-double-fill text-danger"></i>
            Sair do PDV
        </a>
        @endif

        <button class="pdv-side-item" id="btn-dark-mode">
            <i class="ri-moon-line"></i>
            Modo Escuro
        </button>

    </div>
</div>

<div id="pdv-sidebar-overlay" class="pdv-sidebar-overlay"></div>

<input type="hidden" value="{{ $isVendaSuspensa ? $item->id : 0 }}" id="venda_suspensa_id">
<input type="hidden" value="{{ $isComanda ? $item->id : 0 }}" id="pedido_id">
<input type="hidden" id="tipo_divisao_pizza" value="{{ $configCardapio != null ? $configCardapio->valor_pizza : 'divide' }}">
<input type="hidden" id="modelo" value="{{ $modelo }}">


