@extends('layouts.app', ['title' => 'Editar Padrão'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Novo Padrão</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('contador-produto-tributacao.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('contador-produto-tributacao.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('padrao_tributacao._forms')
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    $(document).on("blur", "#inp-cfop_estadual", function () {

        let v = $(this).val().substring(1,4)
        $("#inp-cfop_outro_estado").val('6'+v)
        $("#inp-cfop_entrada_estadual").val('1'+v)
        $("#inp-cfop_entrada_outro_estado").val('2'+v)
    })
</script>
@endsection
