<div class="modal fade" id="modalSangria" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content sangria-modal">

            <div class="modal-header sangria-header">
                <h5 class="modal-title">Sangria de Caixa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label>Valor da sangria</label>
                    <input type="tel" class="form-control sangria-input moeda" id="sangriaValor" placeholder="0,00">
                </div>

                <div class="mb-2">
                    <label>Observação</label>
                    <textarea class="form-control sangria-input" id="sangriaObs" rows="3" placeholder="Motivo da sangria"></textarea>
                </div>

            </div>

            <div class="sangria-footer">
                <button class="btn sangria-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn sangria-btn-primary" id="btnConfirmarSangria">Confirmar</button>
            </div>

        </div>
    </div>
</div>
