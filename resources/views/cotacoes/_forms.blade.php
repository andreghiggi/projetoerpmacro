
<div class="col-md-12 g-2">
    <div class="row">
        <div class="row">
            @if(!isset($item))
            <p class="text-danger">Informe 1 ou mais fornecedores para a cotação</p>

            <div class="col-md-12">
                <label for="">Fornecedores</label>
                <select required class="select2 form-control select2-multiple" data-toggle="select2" name="fornecedor_id[]" multiple="multiple">
                    @foreach($fornecedores as $f)
                    <option value="{{ $f->id }}">{{ $f->info }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <h5>Fornecedor: <strong>{{ $item->fornecedor->info }}</strong></h5>
            @endif
        </div>

        <hr style="margin-top: 10px;">
        <div class="tab-pane" id="produtos" role="tabpanel">
            <div class="card">
                <div class="row">
                    <div class="table-responsive">

                        <table class="table table-dynamic table-produtos">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($item)
                                @foreach($item->itens as $l)
                                <tr class="dynamic-form">
                                    <td>
                                        <select required class="form-control select2 produto_id" name="produto_id[]" id="inp-produto_id">
                                            <option value="{{ $l->produto_id }}">{{ $l->produto->nome }}</option>
                                        </select>
                                    </td>
                                    <td width="30%">
                                        <input required class="form-control qtd" type="tel" value="{{ __moeda($l->quantidade) }}" name="quantidade[]" id="inp-quantidade">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr class="dynamic-form">

                                    <td>
                                        <select required class="form-control select2 produto_id" name="produto_id[]" id="inp-produto_id">
                                        </select>
                                    </td>
                                    <td width="30%">
                                        <input required class="form-control qtd" type="tel" name="quantidade[]" id="inp-quantidade">
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                    <div class="row col-12 col-lg-2 mb-2" style="margin-left: 10px;">
                        <button type="button" class="btn btn-dark btn-add-tr-item">
                            <i class="ri-add-fill"></i>
                            Adicionar Produto
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            {!!Form::text('observacao', 'Observação')
            ->attrs(['class' => ''])
            !!}
        </div>
        <div class="col-md-2">
            {!!Form::select('estado', 'Estado',
            ['nova' => 'Nova',
            'rejeitada' => 'Rejeitada',
            'respondida' => 'Respondida',
            'aprovada' => 'Aprovada'])
            ->attrs(['class' => 'form-select'])->required()
            !!}
        </div>

        <div class="col-md-2">
            {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
            ->attrs(['class' => 'form-select'])
            !!}
        </div>

        <hr class="mt-2">
        <div class="col-12" style="text-align: right;">
            <button type="submit" class="btn btn-success btn-salvar px-5 m-3">Salvar</button>
        </div>

    </div>
</div>

@section('js')
<script type="text/javascript" src="/js/cotacao.js"></script>
@endsection