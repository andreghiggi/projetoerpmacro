<div class="modal fade" id="modalSuprimento" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sangria-modal">

            <div class="modal-header sangria-header">
                <h5 class="modal-title">Suprimento de Caixa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-2">
                    <label>Valor do suprimento</label>
                    <input type="tel" class="form-control sangria-input moeda" id="suprimentoValor" placeholder="0,00">
                </div>

                <div class="mb-2">
                    <label>Tipo de pagamento</label>
                    <select class="form-control form-select sangria-select" id="suprimentoTipoPagamento">
                        <option value="">Selecione</option>
                        @foreach($tiposPagamento as $key => $t)
                        <option value="{{ $key }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <label>Observação</label>
                    <textarea class="form-control sangria-input" id="suprimentoObs" rows="3" placeholder="Motivo do suprimento"></textarea>
                </div>

            </div>

            <div class="sangria-footer">
                <button class="btn sangria-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn sangria-btn-primary" id="btnConfirmarSuprimento">Confirmar</button>
            </div>

        </div>
    </div>
</div>
