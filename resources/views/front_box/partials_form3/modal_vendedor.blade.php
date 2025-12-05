<div class="modal fade modal-vendedor" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Selecione o vendedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::select('vendedor', 'Vendedor', ["" => "Selecione"] + $funcionarios->pluck('nome', 'id')->all())
                        ->id('vendedor')
                        ->value(isset($item) ? $item->funcionario_id : null)
                        ->attrs(['class' => 'select-vendedor']) !!}
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-seleciona-vendedor" data-bs-dismiss="modal">Selecionar</button>
            </div>
        </div>
    </div>
</div>
