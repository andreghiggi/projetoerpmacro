
<div class="modal fade" id="modal-escolhe-cartao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Selecione o cartão</h5>
        
      </div>
      <div class="modal-body">

        @foreach($tipos_pagamento as $t)
        @if($t != 'Dinheiro' && $t != 'Pix' && $t != 'Pix pelo App' && $t != 'Cartão pelo App')
        <div class="row select-card" onclick="setCartao('{{ $t }}')">
          <div class="col-3 mt-2">
            @if($t == 'Visa crédito' || $t == 'Visa débito')
            <img src="/delivery/payments_img/visa.png" style="height: 30px; float: right;">
            @elseif($t == 'Elo crédito' || $t == 'Elo débito')
            <img src="/delivery/payments_img/elo.png" style="height: 30px; float: right;">
            @elseif($t == 'Hipercard crédito' || $t == 'Hipercard débito')
            <img src="/delivery/payments_img/hipercard.png" style="height: 30px; float: right;">
            @elseif($t == 'Mastercard crédito' || $t == 'Mastercard débito')
            <img src="/delivery/payments_img/mastercard.png" style="height: 30px; float: right;">
            @endif
          </div>
          <div class="col-9 mt-2">
            <h5 style="margin-top: 5px">{{ $t }}</h5>
          </div>
        </div>
        @endif
        @endforeach

      </div>
    </div>
  </div>
</div>