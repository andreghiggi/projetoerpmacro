<div class="row g-2">
    <div class="col-md-2">
        <label for="">Local saída</label>

        <select id="inp-local_saida_id" required class="select2 class-required" data-toggle="select2" name="local_saida_id">
            <option value="">Selecione</option>
            @foreach(__getLocaisAtivoUsuario() as $local)
            <option value="{{ $local->id }}">{{ $local->descricao }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label for="">Local entrada</label>

        <select id="inp-local_entrada_id" required class="select2 class-required" data-toggle="select2" name="local_entrada_id">
            <option value="">Selecione</option>
            @foreach(__getLocaisAtivoUsuario() as $local)
            <option value="{{ $local->id }}">{{ $local->descricao }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-8">
        {!!Form::text('observacao', 'Observação')
        !!}
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row m-3">
                <div class="table-responsive">
                    <table class="table table-dynamic">
                        <thead class="table-dark">
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Observação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="dynamic-form">

                                <td style="width: 600px">
                                    <select required class="form-control select2 produto_id" name="produto_id[]" id="inp-produto_id">
                                    </select>
                                </td>
                                <td style="width: 180px">
                                    <input type="tel" class="form-control quantidade" name="quantidade[]" required>
                                </td>
                                <td style="width: 400px">
                                    <input type="text" class="form-control ignore" name="observacao_item[]">
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-remove-tr">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row col-12 col-lg-2 mt-3">
                    <br>
                    <button type="button" class="btn btn-dark btn-add-tr-prod px-2">
                        <i class="ri-add-fill"></i>
                        Adicionar Produto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>