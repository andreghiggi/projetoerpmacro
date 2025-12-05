<div class="modal fade" id="fechamento_caixa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog ">
        <div class="modal-content fechamento-modal">

            <div class="modal-header fechamento-header">
                <h5 class="modal-title">Fechar Caixa</h5>
                <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <h4 class="fw-bold">
                    Valor em Caixa: 
                    <span class="text-success">
                        R$ {{ __moeda($totalVendas + $item->valor_abertura - $somaSangrias + $somaSuprimentos) }}
                    </span>
                </h4>

                <small class="text-muted d-block mb-3">
                    (Calculado como: Suprimentos + Vendas recebidas - Sangrias)
                </small>

                {!! Form::open()->post()->route('caixa.fechar')->multipart() !!}
                <input type="hidden" name="valor_fechamento" value="{{ $totalVendas + $item->valor_abertura }}">
                <input type="hidden" name="caixa_id" value="{{ $item->id }}">

                <div class="mb-3">
                    {!! Form::tel('valor_dinheiro', 'Total em Dinheiro')->attrs(['class' => 'moeda']) !!}
                </div>

                <div class="mb-3">
                    {!! Form::tel('valor_cheque', 'Valor em Cheque')->attrs(['class' => 'moeda']) !!}
                </div>

                <div class="mb-3">
                    {!! Form::tel('valor_outros', 'Valor em Outros')->attrs(['class' => 'moeda']) !!}
                </div>

                <div class="mb-3">
                    {!! Form::text('observacao', 'Observação') !!}
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fechar-btn">
                    Salvar Fechamento
                </button>

                {!! Form::close() !!}
            </div>

        </div>
    </div>
</div>
