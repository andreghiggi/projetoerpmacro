<div class="modal fade" id="dados_dimensao" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label>Vlr. M2/Unit</label>
                        <input type="tel" class="form-control moeda" id="dimensao_valor_unitario_m2">
                    </div>
                    <div class="col-md-2">
                        <label>Largura</label>
                        <input type="tel" class="form-control" id="dimensao_largura">
                    </div>
                    <div class="col-md-2">
                        <label>Altura</label>
                        <input type="tel" class="form-control" id="dimensao_altura">
                    </div>
                    <div class="col-md-2">
                        <label>Quantidade</label>
                        <input type="tel" class="form-control moeda" id="dimensao_quantidade">
                    </div>
                    <div class="col-md-2">
                        <label>M2/Total</label>
                        <input type="tel" class="form-control" id="dimensao_m2_total">
                    </div>
                    <div class="col-md-2">
                        <label>Espessura</label>
                        <input type="tel" class="form-control moeda" id="dimensao_espessura">
                    </div>
                    <div class="col-md-2">
                        <label>Sub total</label>
                        <input type="tel" class="form-control moeda" id="dimensao_sub_total">
                    </div>
                    <div class="col-md-8">
                        <label>Observação</label>
                        <input type="text" class="form-control" id="dimensao_observacao">
                    </div>
                    <div class="col-md-2">
                        <br>
                        <button type="button" class="btn btn-primary w-100" id="btn-add-dimensao">
                            Adicionar
                        </button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Vlr. M2/Unit</th>
                                    <th>Largura</th>
                                    <th>Altura</th>
                                    <th>Quantidade</th>
                                    <th>M2/Total</th>
                                    <th>Espessura</th>
                                    <th>Sub total</th>
                                    <th>Observação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead> 
                            <tbody>
                                
                            </tbody>     
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <label>Total</label>
                        <input type="tel" class="form-control moeda" id="dimensao_total">
                    </div>

                    <div class="col-md-8"></div>
                    <div class="col-md-2">
                        <br>
                        <button type="button" class="btn btn-success w-100" id="btn-salvar-dimensao">
                            Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

