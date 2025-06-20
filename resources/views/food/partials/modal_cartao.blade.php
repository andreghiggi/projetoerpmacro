

<div class="modal fade" id="modal-cartao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Dados do cartão</h5>
        
      </div>
      <form method="post" action="{{ route('food.pagamento-cartao') }}" id="paymentFormCartao">
        @csrf
        <input type="hidden" name="link" value="{{ $config->loja_id }}">
        
        <div class="modal-body">
          <div class="row">
            <input type="hidden" class="carrinho_id" name="carrinho_id">
            <input type="hidden" class="total" name="total">
            <input type="hidden" class="tipo_pagamento" name="tipo_pagamento">
            <input type="hidden" class="observacao" name="observacao">
            
            <div class="col-12 col-md-6">
              <label>Titular do cartão</label>
              <input required type="text" class="form-control" data-checkout="cardholderName" id="cardholderName">
            </div>
            <div class="col-12 col-md-2">
              <label>Tipo do documento</label>
              <select required class="w-100" id="docType" data-checkout="docType">
                <option value="CPF">CPF</option>
                <option value="CNPJ">CNPJ</option>
              </select>
            </div>

            <div class="col-12 col-md-4">
              <label>Número do documento</label>
              <input required type="text" class="form-control cpf_cnpj" data-checkout="docNumber" id="docNumber" name="docNumber">
            </div>

            <div class="col-12 col-md-4">
              <label>Email</label>
              <input required type="email" class="form-control" id="email" name="email">
            </div>

            <div class="col-md-6">
              <label>Número do cartão</label>
              <div class="row">
                <div class="col-md-10">
                  <input required data-checkout="cardNumber" id="cardNumber" type="tel" class="form-control" data-mask="0000000000000000">
                </div>
                <div class="col-md-2">
                  <img id="band-img" style="width: 30px;">
                </div>
              </div>
            </div>

            <div class="col-md-4 col-12">
              <label>Parcelas</label>
              <select required name="installments" data-checkout="installments" id="installments" class="form-control w-100 installments"></select>
            </div>

            <div class="col-md-3">
              <label>Código de segurança</label>
              <input required data-checkout="securityCode" id="securityCode" type="tel" class="form-control">
            </div>

            <div class="col-md-4">
              <label>Data de Vencimento</label>
              <div class="row">
                <div class="col-md-6">
                  <input required placeholder="MM" data-checkout="cardExpirationMonth" id="cardExpirationMonth" type="tel" class="form-control" data-mask="00">
                </div>
                <div class="col-md-6">
                  <input required placeholder="AA" data-checkout="cardExpirationYear" id="cardExpirationYear" type="tel" class="form-control" data-mask="00">
                </div>
              </div>
            </div>
            <div style="visibility: hidden" class="form-group">
              <select class="custom-select" id="issuer" name="issuer" data-checkout="issuer">
              </select>
            </div>
            <input style="visibility: hidden" name="paymentMethodId" id="paymentMethodId"/>
            <input style="visibility: hidden" name="transactionAmount" id="transactionAmount" value="{{$carrinho->valor_total}}" />

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
          <button type="submit" class="btn btn-main text-white">Pagar com cartão</button>
        </div>
      </form>
    </div>
  </div>
</div>

