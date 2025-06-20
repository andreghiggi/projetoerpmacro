
<div class="modal" tabindex="-1" role="dialog" id="mdpix">
  <div class="modal-dialog" role="document">
    <form method="post" action="{{ route('food.pagamento-pix') }}" id="form-pix">
      @csrf
      <div class="modal-content">
        <div class="modal-header fixed-bottom">
          <h3 class="modal-title pull-left" id="tituloProduto">PIX</h3>
          <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
            <i class="lni lni-close"></i>
          </button>
        </div>
        <div class="modal-body">
          <input type="text" class="maskcpf" name="cpf" id="inp-cpf" placeholder="CPF">
          <input type="hidden" value="{{ $carrinho->id }}" class="carrinho_id" name="carrinho_id">
          <input type="hidden" value="Pix pelo App" class="tipo_pagamento" name="tipo_pagamento">
          <input type="hidden" class="observacao" name="observacao" id="inp-observacao">
          <input type="hidden" name="link" value="{{ $config->loja_id }}">

        </div>
        <div class="modal-footer">
          <button type="button" class="botao-acao btn text-white btn-pix" style="float: right;">Finalizar</button>
        </div>
      </div>
    </form>
  </div>
</div>


