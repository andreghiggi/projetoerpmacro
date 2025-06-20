@extends('layouts.app', ['title' => 'Vincular Imagens'])

@section('content')

<div class="card mt-1">
	<div class="card-header">
		<h4>Vincular Imagens</h4>
		<div style="text-align: right; margin-top: -35px;">
			<a href="{{ route('produtos.index') }}" class="btn btn-danger btn-sm px-3">
				<i class="ri-arrow-left-double-fill"></i>Voltar
			</a>
		</div>
	</div>
	<div class="card-body">
		{!!Form::open()
		->post()
		->route('produtos.vincular-imagens')
		->multipart()
		!!}
		<div class="pl-lg-4">
			<div class="row">
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Imagem</th>
								<th>Produto</th>
								<th>Ação</th>
							</tr>
						</thead>
						<tbody>
							@foreach($data as $f)
							<tr>
								<td>
									<img src="{{ $f['img'] }}" class="img-60">
									<input type="hidden" name="diretorio[]" value="{{ $f['diretorio'] }}">
								</td>
								<td>
									<select required class="form-control produto_id" name="produto_id[]"></select>
								</td>
								<td>
									<button type="button" class="btn btn-delete-row btn-sm btn-danger">
										<i class="ri-delete-bin-line"></i>
									</button>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<div class="col-12" style="text-align: right;">
				<button type="submit" class="btn btn-success px-5">Salvar</button>
			</div>
		</div>
		{!!Form::close()!!}
	</div>
</div>
@endsection

@section('js')
<script type="text/javascript">
	$(".btn-delete-row").on("click", function (e) {
		e.preventDefault();

		swal({
			title: "Você está certo?",
			text: "Uma vez deletado, você não poderá recuperar esta imagem!",
			icon: "warning",
			buttons: true,
			buttons: ["Cancelar", "Excluir"],
			dangerMode: true,
		}).then((isConfirm) => {
			if (isConfirm) {
				$(this).closest('tr').remove()

			} else {
				swal("", "Este item está salvo!", "info");
			}
		});
	});

	$(".produto_id").select2({
        minimumInputLength: 2,
        language: "pt-BR",
        placeholder: "Digite para buscar o produto",
        width: "100%",
        ajax: {
            cache: true,
            url: path_url + "api/produtos",
            dataType: "json",
            data: function (params) {
                let empresa_id = $('#empresa_id').val()

                console.clear();

                var query = {
                    pesquisa: params.term,
                    empresa_id: empresa_id,
                    usuario_id: $('#usuario_id').val(),
                };
                // console.log(query)
                return query;
            },
            processResults: function (response) {
                var results = [];

                $.each(response, function (i, v) {
                    var o = {};
                    o.id = v.id;
                    if(v.codigo_variacao){
                        o.codigo_variacao = v.codigo_variacao
                    }

                    if(v.tipo_dimensao == 1){
                        o.espessura = v.espessura
                    }

                    if(v.altura){
                        o.altura = v.altura
                    }

                    o.text = v.nome
                    
                    o.value = v.id;
                    results.push(o);
                });
                return {
                    results: results,
                };
            },
        },
    });
</script>
@endsection
