<div class="row g-2">
    <div class="col-md-4">
        {!!Form::text('descricao', 'Descrição')->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-dynamic">
                <thead class="table-dark">
                    <tr>
                        <th></th>
                        <th>Nome</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($item)
                    @foreach($item->itens as $l)
                    <tr class="dynamic-form">
                        <td width="30">
                            <br>
                            <button class="btn btn-danger btn-remove-tr btn-sm">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                        <td>
                            {!!Form::text('nome[]', '')->required()
                            ->value($l->nome)
                            !!}
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr class="dynamic-form">
                        <td width="30">
                            <br>
                            <button class="btn btn-danger btn-remove-tr btn-sm">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                        <td>
                            {!!Form::text('nome[]', '')->required()
                            !!}
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="row col-12 col-lg-2 mt-3">
            <br>
            <button type="button" class="btn btn-dark btn-add-tr px-2">
                <i class="ri-add-fill"></i>
                Adicionar linha
            </button>
        </div>
    </div>

    <hr>
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>
