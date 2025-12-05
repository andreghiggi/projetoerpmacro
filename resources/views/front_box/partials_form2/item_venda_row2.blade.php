<div class="d-flex justify-content-between align-items-center d-item border-bottom py-2 product-line product-line-{{$code}}">
    <div>
        <div class="fw-semibold text-uppercase small mb-1">{{ substr($item->nome, 0, 30) }}</div>
        <div class="text-muted small"><span class="unitario">R$ {{ __moeda($item->valor_unitario) }}</span> {{ $item->unidade }}</div>
        <div class="text-primary sub_total">R$ {{ __moeda($item->valor_unitario * $qtd) }}</div>

        <button type="button" class="btn btn-dark btn-sm btn-detalhes" style="padding: 2px; padding-left: 10px; padding-right: 10px; font-size: 12px">
            <i class="ri-sticky-note-line"></i>
            detalhes do item
        </button>
    </div>
    <div class="d-flex align-items-center gap-1">
        <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-1 btn-subtrai">-</button>
        <input type="text" class="form-control text-center px-2 py-1 qtd" value="{{ $qtd }}" style="width: 60px;" readonly>
        <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-1 btn-adiciona">+</button>
        @if($isAdmin)
        <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1 ms-1 btn-remove" onclick="removeItem('{{$code}}')">×</button>
        @endif
    </div>

    <input type="hidden" class="produto_id" name="produto_id[]" value="{{ $item->id }}">
    <input type="hidden" class="valor_original" value="{{ $item->valor_unitario }}">

    <input type="hidden" class="quantidade" name="quantidade[]" value="{{ $qtd }}">
    <input type="hidden" class="valor_unitario" name="valor_unitario[]" value="{{ ($item->valor_unitario) }}">
    <input type="hidden" class="subtotal_item" name="subtotal_item[]" value="{{ ($item->valor_unitario*$qtd) }}">

    <!-- variaveis -->
    <input type="hidden" class="observacao" name="observacao[]" value="{{ $item->observacao }}">
    <input type="hidden" class="adicionais" name="adicionais[]" value="">
    <input type="hidden" class="tamanho_id" name="tamanho_id[]" value="{{ $item->tamanho_id }}">
    <input type="hidden" class="sabores" name="sabores[]" value="">
    <input type="hidden" class="code" value="{{$code}}">
</div>