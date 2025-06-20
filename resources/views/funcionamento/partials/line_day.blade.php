@foreach($dias as $key => $d)
<tr>
	<input type="hidden" name="dia[]" value="{{$key}}">
	<td>
		{!!Form::text('', '')->attrs(['class' => ''])->readonly()
		->value($d)
        !!}
	</td>
	<td>
		{!!Form::text('inicio[]', '')->attrs(['class' => 'timer'])->required()
        !!}
	</td>
	<td>
		{!!Form::text('fim[]', '')->attrs(['class' => 'timer'])->required()
        !!}
	</td>
</tr>
@endforeach