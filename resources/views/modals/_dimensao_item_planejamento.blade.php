<div class="modal fade" id="modal_dimensao_planejamento" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label>Quantidade</label>
                        <input type="tel" class="form-control moeda calcular-dimensao" id="dimensao_quantidade">
                    </div>
                    <div class="col-md-2">
                        <label>Vlr. Unit</label>
                        <input type="tel" class="form-control moeda calcular-dimensao" id="dimensao_valor_unitario">
                    </div>
                    <div class="col-md-2">
                        <label>Largura</label>
                        <input type="tel" class="form-control inp-dimensao calcular-dimensao" id="dimensao_largura">
                    </div>

                    <div class="col-md-2">
                        <label>Comprimento</label>
                        <input type="tel" class="form-control inp-dimensao calcular-dimensao" id="dimensao_comprimento">
                    </div>

                    <div class="col-md-2">
                        <label>Espessura</label>
                        <input type="tel" class="form-control inp-dimensao calcular-dimensao" id="dimensao_espessura">
                    </div>

                    <div class="col-md-2">
                        <label>Peso Específico</label>
                        <input type="tel" class="form-control inp-dimensao calcular-dimensao" id="dimensao_peso_especifico">
                    </div>

                    <div class="col-md-2">
                        <br>
                        <!-- <label>Peso Bruto</label> -->
                        <!-- <input type="checkbox" class="form-control inp-dimensao calcular-peso_bruto" id="calcular_peso_bruto"> -->
                        <input type="checkbox" class="form-check-input ml-3 calcular-peso_bruto" id="calcular_peso_bruto">
                        <label>Calcular Peso Bruto</label>
                    </div>

                    <div class="col-md-2">
                        <label>Peso Bruto</label>
                        <input type="tel" class="form-control inp-dimensao calcular-dimensao" id="dimensao_peso_bruto">
                    </div>
                    
                    <div class="col-md-2">
                        <label>Sub total</label>
                        <input type="tel" class="form-control moeda" id="dimensao_sub_total">
                    </div>
                    
                </div>

            </div>
            <div class="modal-footer">
                <div class="col-md-2">
                    <button type="button" class="btn btn-success w-100" id="btn-salvar-dimensao">
                        Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

