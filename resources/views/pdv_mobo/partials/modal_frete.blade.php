<div class="modal fade" id="modalFrete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content frete-modal">

            <div class="modal-header sangria-header">
                <h5 class="modal-title">Frete da venda</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row">
                    <div class="col-6 mb-1">
                        <label>Valor do frete</label>
                        <input type="tel" class="form-control sangria-input moeda" id="freteValor" placeholder="0,00">
                    </div>
                    <div class="col-6 mb-1">
                        <label>Qtd. volumes</label>
                        <input type="tel" class="form-control sangria-input" id="freteQtdVolumes" placeholder="0">
                    </div>
                    <div class="col-6 mb-1">
                        <label>Núm. volumes</label>
                        <input type="tel" class="form-control sangria-input" id="freteNumVolumes" placeholder="0">
                    </div>

                    <div class="col-6 mb-1">
                        <label>Peso bruto</label>
                        <input type="tel" class="form-control sangria-input peso" id="fretePresoBruto" placeholder="0">
                    </div>
                    <div class="col-6 mb-1">
                        <label>Peso liquído</label>
                        <input type="tel" class="form-control sangria-input peso" id="fretePresoLiquido" placeholder="0">
                    </div>

                    <div class="col-6 mb-1">
                        <label>Espécie</label>
                        <input type="text" class="form-control sangria-input" id="freteEspecie" placeholder="">
                    </div>

                    <div class="col-12 mb-1">
                        <label>Transportadora</label>
                        <select class="form-control form-select sangria-select" id="freteTransportadora">
                            <option value="">Selecione</option>
                            @foreach($transportadoras as $t)
                            <option value="{{ $t->id }}">{{ $t->razao_social }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 mb-1">
                        <label>Tipo</label>
                        <select class="form-control form-select sangria-select" id="freteTipo">
                            @foreach(App\Models\Nfe::tiposFrete() as $key => $t)
                            <option value="{{ $key }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 mb-1">
                        <label>Placa</label>
                        <input type="text" class="form-control sangria-input placa" id="fretePlaca" placeholder="AAA-0A00">
                    </div>

                    <div class="col-6 mb-1">
                        <label>UF</label>
                        <select class="form-control form-select sangria-select" id="freteUf">
                            <option value="">Selecione</option>
                            @foreach(App\Models\Cidade::estados() as $e)
                            <option value="{{ $e }}">{{ $e }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="sangria-footer">
                <button class="btn sangria-btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn sangria-btn-primary" id="btnConfirmarFrete">Confirmar</button>
            </div>

        </div>
    </div>
</div>
