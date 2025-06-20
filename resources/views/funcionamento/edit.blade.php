@extends('layouts.app', ['title' => 'Horário de Atendimento'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Novo horário de atendimento</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('funcionamentos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">

        {!!Form::open()
        ->put()
        ->route('funcionamentos.update', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('funcionamento._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>

@endsection
@section('js')
<script type="text/javascript">
    $(document).on("change", "#inp-funcionario_id", function () {
        let funcionario_id = $(this).val()
        $.get(path_url + "api/funcionamentos/diasDoFuncionario", {funcionario_id: funcionario_id})
        .done((success) => {
            $('#table-horarios tbody').html(success)
        })
        .fail((err) => {
            console.log(err)
            swal("Erro", "Erro ao buscar dados do funcionário", "error")
        })
    });
</script>
@endsection
