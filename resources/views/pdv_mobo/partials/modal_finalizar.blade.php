<div class="modal fade" id="modalFinalizar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content finalizar-modal">

            <div class="modal-header finalizar-header">
                <h5 class="modal-title">Finalizar Venda</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- TOTAL -->
                <div class="finalizar-total-box text-center mb-3">
                    <span id="finalizarTotal" class="finalizar-total">R$ 0,00</span>
                    <div class="finalizar-label">Total da venda</div>
                </div>

                <!-- DESCONTO -->
                <div class="mb-1 div-cpf_cnpj">
                    <label>CPF/CNPJ</label>
                    <input type="tel" class="form-control finalizar-input cpf_cnpj" id="cpfNota" placeholder="Documento">
                </div>

                <div class="mb-1">
                    <label>Desconto</label>
                    <input type="tel" class="form-control finalizar-input moeda" id="finalizarDesconto" placeholder="0,00">
                </div>

                <div class="mb-2">
                    <label>Acréscimo</label>
                    <input type="tel" class="form-control finalizar-input moeda" id="finalizarAcrescimo" placeholder="0,00">
                </div>

                <!-- FORMAS DE PAGAMENTO -->
                <label class="form-label">Tipos de pagamento</label>
                <div class="pagamentos-grid mb-2">
                    @foreach($tiposPagamento as $key => $t)
                    <button class="pg-btn" data-pg="{{ $key }}">{{ $t }}</button>
                    @endforeach
                </div>

                <div class="mb-1 d-flex gap-2 align-items-center">

                    <input type="tel" 
                    class="form-control finalizar-input moeda" 
                    id="valorForma" 
                    placeholder="Valor desta forma">

                    <input type="date" 
                    class="form-control finalizar-input" 
                    id="vencimentoForma"
                    style="max-width:150px;">
                </div>

                <button class="btn btn-dark w-100" id="btnAdicionarForma">
                    Adicionar
                </button>

                <!-- LISTA DE FORMAS ADICIONADAS -->
                <div id="listaPagamentos" class="mb-3"></div>

                <!-- RESUMO -->
                <div class="d-flex justify-content-between mt-2">
                    <div>
                        <small class="finalizar-label">Total a pagar</small><br>
                        <strong id="resumoTotal">R$ 0,00</strong>
                    </div>
                    <div>
                        <small class="finalizar-label">Total pago</small><br>
                        <strong id="resumoPago">R$ 0,00</strong>
                    </div>
                    <div>
                        <small class="finalizar-label">Falta</small><br>
                        <strong id="resumoFalta">R$ 0,00</strong>
                    </div>
                </div>

                <!-- OBS -->
                <div class="mt-3">
                    <label class="form-label">Observação</label>
                    <textarea class="form-control finalizar-input" rows="2" id="finalizarObs"></textarea>
                </div>

            </div>

            <div class="finalizar-footer">
                <button class="btn finalizar-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn finalizar-btn-primary" id="btnConfirmarVenda">Confirmar Venda</button>
            </div>

        </div>
    </div>
</div>
