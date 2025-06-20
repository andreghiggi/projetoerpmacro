<div class="modal fade" id="fechamento_caixa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Fechar Caixa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {!!Form::open()
                    ->post()
                    ->route('caixa.fechar')
                    ->multipart()
                    !!}
                    <div class="col-md-12">
                        <h4>Valor Total: <strong class="text-success">R$ {{ __moeda($totalVendas + $valor_abertura) }}</strong></h4>
                    </div>
                    <input type="hidden" name="valor_fechamento" value="{{ $totalVendas + $valor_abertura }}">
                    <input type="hidden" name="caixa_id" value="{{ $item->id }}">
                    <div class="col-md-12 mt-3">
                        {!! Form::tel('valor_dinheiro', 'Total em Dinheiro')->attrs(['class' => 'moeda']) !!}
                    </div>
                    <div class="col-md-12 mt-3">
                        {!! Form::tel('valor_cheque', 'Valor em Cheque')->attrs(['class' => 'moeda']) !!}
                    </div>
                    <div class="col-md-12 mt-3">
                        {!! Form::tel('valor_outros', 'Valor em Outros')->attrs(['class' => 'moeda']) !!}
                    </div>
                    <div class="col-md-12 mt-3">
                        {!! Form::text('observacao', 'Observação')->attrs(['class' => '']) !!}
                    </div>
                    <div class="mt-3 ms-auto">
                        <button type="submit" class="btn btn-primary px-3 w-100" data-bs-dismiss="modal">Salvar Fechamento</button>
                    </div>
                    {!!Form::close()!!}
                </div>
            </div>
           
        </div>
    </div>
</div>
