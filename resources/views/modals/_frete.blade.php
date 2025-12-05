<div class="modal fade" id="modal_frete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title text-white" id="staticBackdropLabel">Frete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-2">
                        {!!Form::tel('valor_frete', 'Valor do frete')
                        ->attrs(['class' => 'moeda'])
                        ->id('valor_frete')
                        ->value(isset($item) ? __moeda($item->valor_frete) : '')
                        !!}
                    </div>

                    <div class="col-md-4">
                        <label>Transportadora</label>
                        <select type="tel" class="form-select" id="inp-transportadora_id" name="transportadora_id">
                            @if(isset($item) && $item->transportadora)
                            <option value="{{ $item->transportadora_id }}">{{ $item->transportadora->razao_social }} - 
                                {{ $item->transportadora->cpf_cnpj }}
                            </option>
                            @endif
                        </select>
                    </div>

                    <div class="col-md-2">
                        {!!Form::tel('qtd_volumes', 'Qtd de Volumes')
                        ->attrs(['class' => ''])
                        !!}
                    </div>
                    <div class="col-md-2">
                        {!!Form::tel('numeracao_volumes', 'Número de Volumes')
                        ->attrs(['class' => ''])
                        !!}
                    </div>
                    <div class="col-md-2">
                        {!!Form::tel('peso_bruto', 'Peso Bruto')
                        ->attrs(['class' => 'peso'])
                        !!}
                    </div>
                    <div class="col-md-2">
                        {!!Form::tel('peso_liquido', 'Peso Líquido')
                        ->attrs(['class' => 'peso'])
                        !!}
                    </div>
                    <div class="col-md-3">
                        {!!Form::text('especie', 'Espécie')
                        ->attrs(['class' => ''])
                        !!}
                    </div>
                    <div class="col-md-3">
                        {!!Form::select('tipo', 'Tipo', App\Models\Nfe::tiposFrete())
                        ->attrs(['class' => 'form-select'])
                        !!}
                    </div>

                    <div class="col-md-2">
                        {!!Form::text('placa', 'Placa')
                        ->attrs(['class' => 'placa'])
                        !!}
                    </div>
                    <div class="col-md-2">
                        {!!Form::select('uf', 'UF', ['' => 'Selecione'] + App\Models\Cidade::estados())
                        ->attrs(['class' => 'form-select'])
                        !!}
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-save-frete">
                    <i class="ri-checkbox-circle-line"></i>
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>