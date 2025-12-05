@extends('layouts.app', ['title' => 'Editar Garantia'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Garantia</h4>

        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('garantias.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('garantias.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            @include('garantias._forms')
        </div>
        {!!Form::close()!!}

    </div>
</div>
@endsection
@section('js')
<script type="text/javascript">
    $("body").on("change", "#inp-produto_id", function () {
        $.get(path_url + "api/produtos/findId/" + $(this).val())
        .done((res) => {
            $('#inp-prazo_garantia').val(res.prazo_garantia)
        })
        .fail((err) => {
            console.log(err)
        })
    })
</script>
@endsection
