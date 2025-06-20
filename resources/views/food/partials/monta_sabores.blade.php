<hr>
<p class="text-muted" style="margin-top: 5px; font-size: 20px">Sabores adicionados</p>
@foreach($sabores as $key => $p)

<div class="row" style="margin-top: 7px; box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px; margin-left: 5px; margin-right: 5px;">
	<div class="col-md-3 col-12">
		<img style="height: 50px; border-radius: 10px; margin-top: 7px; margin-bottom: 7px;" src="{{ $p->img }}">
	</div>
	<div class="col-md-6 col-8" style="margin-top: 20px;">
		<label>{{ $key+1 }}/{{ sizeof($sabores) }} {{ $p->nome }}</label>
	</div>
	<div class="col-md-3 col-4">
		<a class="btn btn-sm" style="margin-top: 15px" onclick="remove_sabor('{{ $p->id }}')">
			<i style="font-size: 23px" class="lni lni-trash text-danger"></i>
		</a>
	</div>
</div>

@endforeach

