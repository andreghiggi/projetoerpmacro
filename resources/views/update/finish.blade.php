@extends('layouts.app', ['title' => 'Update'])
@section('content')
<div class="mt-3">
	<div class="card card-custom gutter-b example example-compact">
		<div class="container @if(env('ANIMACAO')) animate__animated @endif animate__backInLeft">
			<div class="col-lg-12">
				<br>
				@foreach($logMessage as $log)
				<p>{!! $log !!}</p>
				@endforeach
			</div>

			<a href="/update-sql" class="btn btn-info mb-4 ml-4">
				<i class="ri-arrow-left-circle-line"></i>
				voltar
			</a>
		</div>
	</div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
	
</script>
@endsection