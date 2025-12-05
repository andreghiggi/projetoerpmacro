<div class="col-md-4">
	<table class="table">
		<thead>
			<tr>
				<th>Data</th>
				<th>Valor</th>
			</tr>
		</thead>
		<tbody>
			@foreach($datas as $m)
			<tr>
				<td>
					<input class="form-control" type="date" name="dt_recorrencia[]" value="{{$m}}">
				</td>
				<td>
					<input class="form-control" type="tel" name="valor_recorrencia[]" value="{{$valor}}">
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>