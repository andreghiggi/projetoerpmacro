@extends('layouts.app', ['title' => 'Agendamentos'])
@section('content')

<div class="mt-1">
    <input type="hidden" id="agendamentos" value="{{ json_encode($agendamentos) }}">
    <div class="row">
        <div class="card">
            <div class="card-body">

                {!!Form::open()->fill(request()->all())
                ->get()
                !!}
                <div class="row mt-3">

                    <div class="col-md-3">
                        {!!Form::select('funcionario_id', 'Atendente', ['' => 'Todos'] + $funcionarios->pluck('nome', 'id')->all())
                        ->id('funcionario')
                        ->attrs(['class' => 'form-select'])
                        !!}
                    </div>
                    <div class="col-md-3 text-left">
                        <br>
                        <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                        <a id="clear-filter" class="btn btn-danger" href="{{ route('agendamentos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                    </div>
                </div>
                {!!Form::close()!!}

                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div id="external-events" class="mt-3">
                            </div>
                            <div class="col-lg-12">

                                <div class="">
                                    <div id="calendar" class="calendario"></div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->

                    </div>

                    <div id="row" class="mt-2">
                        <div class="external-event bg-success-subtle text-success" data-class="bg-success"><i class="ri-focus-fill me-2 vertical-middle"></i>Agendamento finalizado</div>
                        <div class="external-event bg-primary-subtle text-primary" data-class="bg-primary"><i class="ri-focus-fill me-2 vertical-middle"></i>Agendamento prioridade baixa</div>
                        <div class="external-event bg-warning-subtle text-warning" data-class="bg-warning"><i class="ri-focus-fill me-2 vertical-middle"></i>Agendamento prioridade média</div>
                        <div class="external-event bg-danger-subtle text-danger" data-class="bg-danger"><i class="ri-focus-fill me-2 vertical-middle"></i>Agendamento prioridade alta</div>
                    </div>

                    <!-- end col-12 -->
                </div> <!-- end row -->
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="create_permission" value="@can('agendamento_create') 1 @else 0 @endcan">

@include('modals._agendamento')

@section('js')

<script src="/assets/vendor/fullcalendar/main.min.js"></script>
<script src="/js/calendar.js"></script>
<script src="/js/agendamento.js"></script>

@endsection

@endsection
