@section('css')
<style type="text/css">
    input[type="file"] {
        display: none;
    }

    .file-certificado label {
        padding: 10px 10px;
        width: 100%;
        background-color: #1C1F23;
        color: #FFF;
        text-transform: uppercase;
        text-align: center;
        display: block;
        margin-top: 15px;
        cursor: pointer;
        border-radius: 5px;
    }

</style>
@endsection

<div class="row g-3">
    @if(__countLocalAtivo() > 1)
    <div class="col-md-2">
        <label for="">Local</label>

        <select id="inp-local_id" required class="select2 class-required" data-toggle="select2" name="local_id">
            <option value="">Selecione</option>
            @foreach(__getLocaisAtivoUsuario() as $local)
            <option @isset($item) @if($item->local_id == $local->id) selected @endif @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
            @endforeach
        </select>
    </div>
    @else
    <input id="inp-local_id" type="hidden" value="{{ __getLocalAtivo() ? __getLocalAtivo()->id : '' }}" name="local_id">
    @endif
    <div class="col-md-4">
        {!! Form::select('natureza_id', 'Natureza de operação', ['' => 'Selecione'] + $naturezas->pluck('descricao', 'id')->all())->attrs([
        'class' => 'select2 class-required',
        ])->required() !!}
    </div>

    <div class="col-md-3">
        {!! Form::select('cst', 'CST', App\Models\CteOs::getCsts())->attrs(['class' => 'select2']) !!}
    </div>
    <div class="col-md-1">
        {!! Form::text('perc_icms', '%ICMS')->required()->attrs(['class' => 'perc class-required']) !!}
    </div>
    <div class="col-md-1">
        {!!Form::tel('cfop', 'CFOP')->attrs(['class' => 'cfop class-required'])->required()
        !!}
    </div>
    <div class="col-md-6">
        {!! Form::select(
        'remetente_id',
        'Emitente',
        ['' => 'Selecione'] + $clientes->pluck('razao_social', 'id')->all(),
        )->attrs(['class' => 'select2 class-required'])->required()
        ->value(isset($item) ? $item->emitente_id : null) !!}
        <div class="card mt-3 div-remetente d-none">
            <div class="m-3">
                <h5 style="color: rgb(13, 197, 13)" class="text-center">EMITENTE SELECIONADO</h5>
                <hr>
                <H6>Razão Social: <strong id="razao_social_remetente"></strong></H6>
                <H6>CNPJ: <strong id="cnpj_remetente"></strong></H6>
                <H6>Cidade: <strong id="cidade_remetente"></strong></H6>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        {!! Form::select(
        'destinatario_id',
        'Tomador',
        ['' => 'Selecione'] + $clientes->pluck('razao_social', 'id')->all(),
        )->attrs(['class' => 'select2 class-required'])->required()
        ->value(isset($item) ? $item->tomador_id : null) !!}
        <div class="card mt-3 div-destinatario d-none">
            <div class="m-3">
                <h5 style="color: rgb(13, 197, 13)" class="text-center">TOMADOR SELECIONADO</h5>
                <hr>
                <H6>Razão Social: <strong id="razao_social_destinatario"></strong></H6>
                <H6>CNPJ: <strong id="cnpj_destinatario"></strong></H6>
                <H6>Cidade: <strong id="cidade_destinatario"></strong></H6>
            </div>
        </div>
    </div>
    <hr class="mt-5">
    <div class="row g-3">
        <h3>Informações da Carga</h3>
        <div class="col-md-3">
            {!! Form::select('veiculo_id', 'Veiculo', ['' => 'Selecione'] + $veiculos->pluck('placa', 'id')
            ->all())->attrs(['class' => 'select2'])
            ->required() !!}
        </div>
        <div class="col-md-2">
            {!! Form::select('tomador', 'Tomador', App\Models\CteOs::tiposTomador())->attrs(['class' => 'select2'])
            ->required() !!}
        </div>
        <div class="col-md-2">
            {!! Form::tel('valor_transporte', 'Valor carga')->attrs(['class' => 'moeda'])
            ->required() !!}
        </div>
        <div class="col-md-2">
            {!! Form::tel('valor_receber', 'Valor a receber')->attrs(['class' => 'moeda'])
            ->required() !!}
        </div>
        <div class="col-md-3">
            {!! Form::select('modal', 'Modelo de transporte',
            App\Models\CteOs::modals())->attrs(['class' => 'select2'])->required() !!}
        </div>
    </div>
    <hr class="mt-5">
    <h4>Informação de entrega</h4>
    <h6 class="mt-2" style="color: cornflowerblue">Endereço do tomador</h6>
    <div class="col-md-4">
        {!! Form::select('municipio_envio', 'Município de envio', ['' => 'Selecione'] + $cidades->pluck('info', 'id')->all())->attrs([
        'class' => 'select2',
        ])->required() !!}
    </div>
    <div class="col-md-4">
        {!! Form::select('municipio_inicio', 'Município de início', ['' => 'Selecione'] + $cidades->pluck('info', 'id')->all())->attrs([
        'class' => 'select2',
        ])->required() !!}
    </div>
    <div class="col-md-4">
        {!! Form::select('municipio_fim', 'Município final', ['' => 'Selecione'] + $cidades->pluck('info', 'id')->all())->attrs([
        'class' => 'select2',
        ])->required() !!}
    </div>

    <div class="col-md-6">
        {!! Form::text('descricao_servico', 'Descrição do serviço')->required() !!}
    </div>

    <div class="col-md-2">
        {!! Form::text('quantidade_carga', 'Quantidade de carga')->attrs(['class' => 'qtd'])->required() !!}
    </div>

    <div class="col-md-2">
        {!! Form::date('data_viagem', 'Data de viagem')->required() !!}
    </div>
    <div class="col-md-2">
        {!! Form::text('horario_viagem', 'Horário de viagem')->attrs(['data-mask' => '00:00'])->required() !!}
    </div>

    <div class="col-12 col-md-4">
            <div class="row">
                <div class="col-12 mt-3">
                    <h4>Percurso</h4>
                    <div class="row">
                        <table class="table mb-0 table-striped table-dynamic">
                            <thead class="table-dark">
                                <tr>
                                    <th>UF</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody class="datatable-body" id="tbody">
                                @if (isset($item) && sizeof($item->percurso) > 0)
                                @foreach($item->percurso as $p)
                                <tr class="dynamic-form">
                                    <td class="col-10">
                                        <br>

                                        {!! Form::select('uf[]', '', ['' => 'Selecione...'] + App\Models\Cidade::estados())
                                        ->attrs(['class' => 'select2'])
                                        ->value($p->uf)
                                        !!}
                                    </td>
                                    <td>
                                        <br>
                                        <button class="btn btn-danger btn-sm btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr class="dynamic-form">
                                    <td class="col-10">
                                        <br>
                                        {!! Form::select('uf[]', '', ['' => 'Selecione...'] + App\Models\Cidade::estados())
                                        ->attrs(['class' => 'select2']) !!}
                                    </td>
                                    <td>
                                        <br>
                                        <button class="btn btn-danger btn-sm btn-remove-tr">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <br>
                            <button type="button" class="btn btn-dark btn-add-tr">
                                <i class="ri-add-line"></i>
                                Adicionar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <hr class="mt-5">
    <div class="col-md-12">
        {!! Form::text('observacao', 'Informação adicional') !!}
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5 m-3">Salvar</button>
    </div>
</div>
@section('js')
<script src="/js/cte_os.js"></script>
@endsection
