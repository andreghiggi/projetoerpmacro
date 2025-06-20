<div class="modal fade" id="event-modal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form class="needs-validation" id="form-event" method="post" action="{{ route('agendamentos.store') }}">
                @csrf
                <div class="modal-header py-3 px-4 border-bottom-0">
                    <h5 class="modal-title" id="modal-title">Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="row g-2">
                                <div class="col-lg-6 col-12">
                                    <label for="">Serviços</label>
                                    <select class="select2 form-control select2-multiple" name="servicos[]" data-toggle="select2" multiple="multiple" id="servicos">
                                        @foreach ($servicos as $item)
                                        <option value="{{$item->id}}" data-id="{{$item->id}}" data-valor="{{$item->valor}}" data-tempo="{{$item->tempo_servico}}">{{$item->nome}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-6 col-12">
                                    {!!Form::select('funcionario_id', 'Funcionário')->attrs(['class' => '']) !!}
                                </div>

                            </div>

                            <div class="row mt-2">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <button type="button" class="btn btn-info w-100" id="btn-buscar-horarios">
                                        Buscar Horários
                                        <i class="ri-search-2-fill"></i>
                                    </button>
                                </div>
                                <div class="col-lg-3"></div>

                            </div>

                            <div class="row">
                                <label class="control-label form-label">Horários disponíveis</label>
                                <div class="table-responsive" style="height: 300px; overflow-y: scroll;">
                                    <table class="table" id="tabela-novo-agendamento">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Atendente</th>
                                                <th>Horário</th>
                                                <th>Valor</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="4">
                                                    Busque os horários para exibir na tabela
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                        </div>

                        <div class="row mt-3 g-2">
                            <div class="col-lg-6 col-12">
                                {!!Form::select('cliente_id', 'Cliente')->attrs(['class' => ''])->required() !!}
                            </div>

                            <div class="col-lg-2 col-6">
                                {!!Form::tel('inicio', 'Início')->attrs(['class' => 'timer']) !!}
                            </div>

                            <div class="col-lg-2 col-6">
                                {!!Form::tel('termino', 'Término')->attrs(['class' => 'timer']) !!}
                            </div>

                            <div class="col-lg-2 col-6">
                                {!!Form::tel('desconto', 'Desconto')->attrs(['class' => 'moeda']) !!}
                            </div>
                            <div class="col-lg-2 col-6">
                                {!!Form::tel('total', 'Total')->attrs(['class' => 'moeda'])->required() !!}
                            </div>

                            <div class="col-lg-10 col-12">
                                {!!Form::text('observacao', 'Observação')->attrs(['class' => '']) !!}
                            </div>

                            <div class="col-lg-3 col-6">
                                {!!Form::select('prioridade', 'Prioridade', 
                                ['baixa' => 'Baixa', 'media' => 'Media', 'alta' => 'Alta'])->attrs(['class' => 'form-select']) !!}
                            </div>

                            <input type="hidden" name="funcionario" id="funcionario">
                            <input type="hidden" name="data" id="data">
                        </div>

                    </div>
                    
                </div>
                <div class="modal-footer">

                    <div class="text-end">
                        <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Sair</button>
                        <button type="button" class="btn btn-success" id="btn-save-event">Salvar</button>
                    </div>
                </div>
            </form>
        </div> <!-- end modal-content-->
    </div> <!-- end modal dialog-->
</div>