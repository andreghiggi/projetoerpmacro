
<div class="modal" tabindex="-1" role="dialog" id="modal-escolhe-agendamento">
  <div class="modal-dialog" role="document">
    <form method="post" action="{{ route('food.seta-agendamento') }}" id="form-pix">
      @csrf
      <div class="modal-content">
        <div class="modal-header fixed-bottom">
          <h3 class="modal-title pull-left" id="tituloProduto">Agendamento</h3>
          <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
            <i class="lni lni-close"></i>
          </button>
        </div>
        <div class="modal-body">
          <h4>Atendente: <strong class="atendente"></strong></h4>
          <h5>Início: <strong class="inicio"></strong></h5>
          <h5>Fim: <strong class="fim"></strong></h5>
          <h5>Data: <strong class="data"></strong></h5>

          <input type="hidden" name="funcionario_id" id="funcionario_id">
          <input type="hidden" name="inicio" id="inicio">
          <input type="hidden" name="fim" id="fim">
          <input type="hidden" name="data" id="data">

          <input class="fake-hidden" name="balcao" value="1">
          <input type="hidden" name="link" value="{{ $config->loja_id }}" />
        </div>
        <div class="modal-footer">
          <button type="submit" class="botao-acao btn text-white" style="float: right;">Continuar</button>
        </div>
      </div>
    </form>
  </div>
</div>


