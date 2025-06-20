@extends('layouts.app', ['title' => 'Configuração de Contrato'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Configuração de Contrato</h4>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->post()
        ->route('contrato-config.store')
        ->multipart()
        !!}
        <div class="pl-lg-4">
            @include('config_contrato._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>

<div class="modal fade" id="modal-info" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Variáveis para contrato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    
                    <h5><strong class="text-primary">%nome%</strong> - Nome da empresa</h5>
                    <h5><strong class="text-primary">%nome_fantasia%</strong> - Nome fantasia da empresa</h5>
                    <h5><strong class="text-primary">%cpf_cnpj%</strong> - CPF/CNPJ da empresa</h5>
                    <h5><strong class="text-primary">%ie%</strong> - Inscrição estadual da empresa</h5>
                    <h5><strong class="text-primary">%email%</strong> - Email da empresa</h5>
                    <h5><strong class="text-primary">%celular% </strong>- Celular da empresa</h5>
                    <h5><strong class="text-primary">%endereco%</strong> - Endereço da empresa</h5>
                    <h5><strong class="text-primary">%data%</strong> - Data atual</h5>
                    <h5><strong class="text-primary">%horario%</strong> - Horario atual</h5>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    $(function(){
        tinymce.init({ selector: 'textarea.tiny', language: 'pt_BR'})

        setTimeout(() => {
            $('.tox-promotion, .tox-statusbar__right-container').addClass('d-none')
        }, 500)
    })

    function openModal(){
        $('#modal-info').modal('show')
    }
   
</script>
@endsection
