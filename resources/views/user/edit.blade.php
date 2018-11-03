@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-10">
			<div class="card">
				<div class="card-header">

					<h4 class="title">Form Edit User</h4>

					<button type="button" id="btn-reset" class="btn btn-primary float-right">
						Reset
					</button>
				</div>

				<div class="card-body">

					<form id="form-user" method="POST" action="{{ route('user.store') }}">
						@csrf
						
						{{-- Name --}}
						<div class="form-group row">
							<label for="name" class="col-md-2 col-form-label text-md-right">Name :</label>

							<div class="col-md-10">
								<input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') ?: $user['name'] }}" required placeholder="Nick Name (Min : 3 Character)">

								@if ($errors->has('name'))
									<span class="invalid-feedback" role="alert">
										<strong>{{ $errors->first('name') }}</strong>
									</span>
								@endif

							</div>
						</div>

						{{-- Email --}}

						<div class="form-group row">
							<label for="email" class="col-md-2 col-form-label text-md-right">Email :</label>

							<div class="col-md-10">
								<input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') ?: $user['email'] }}" required placeholder="email@address.com">

								@if ($errors->has('email'))
									<span class="invalid-feedback" role="alert">
										<strong>{{ $errors->first('email') }}</strong>
									</span>
								@endif

							</div>
						</div>

						{{-- Birth --}}

						<div class="form-group row">
							<label for="birth" class="col-md-2 col-form-label text-md-right">Date Of Birth :</label>

							<div class="col-md-10">
								<input id="birth" type="date" class="form-control{{ $errors->has('birth') ? ' is-invalid' : '' }}" name="birth" value="{{ old('birth') ?: $user['birth'] }}" required placeholder="Format (mm / dd / YYYY)">

								@if ($errors->has('birth'))
									<span class="invalid-feedback" role="alert">
										<strong>{{ $errors->first('birth') }}</strong>
									</span>
								@endif

							</div>
						</div>

						{{-- Phone --}}

						<div class="form-group row">
							<label for="phone" class="col-md-2 col-form-label text-md-right">Phone Number :</label>

							<div class="col-md-10">
								<input id="phone" type="number" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') ?: $user['phone'] }}" required placeholder="Ex: 087759625462">

								@if ($errors->has('phone'))
									<span class="invalid-feedback" role="alert">
										<strong>{{ $errors->first('phone') }}</strong>
									</span>
								@endif

							</div>
						</div>

						{{-- Gender --}}

						<div class="form-group row">
							<label for="gender" class="col-md-2 col-form-label text-md-right">Gender :</label>

							<div class="col-md-10">
								<div class="form-check">
								  	<input class="form-check-input" type="radio" name="gender" id="male" value="Male" 
								
								  		@if (old('gender'))
								  			{{ old('gender') == 'Male' ? 'checked' : '' }}
								  		@else
								  			{{ $user['gender'] == 'Male' ? 'checked' : '' }}
								  		@endif
								  	>

								  	<label class="form-check-label" for="male">
								    	Male
								  	</label>
								</div>

								<div class="form-check">
								  	<input class="form-check-input" type="radio" name="gender" id="female" value="Female" 
								  		
								  		@if (old('gender'))
								  			{{ old('gender') == 'Female' ? 'checked' : '' }}
								  		@else
								  			{{ $user['gender'] == 'Female' ? 'checked' : '' }}
								  		@endif

								  	>

								  	<label class="form-check-label" for="female">
								    	Female
								  	</label>
								</div>

								@if ($errors->has('gender'))
									<span class="invalid-feedback" style="display: block" role="alert">
										<strong>{{ $errors->first('gender') }}</strong>
									</span>
								@endif

							</div>
						</div>

						{{-- Address --}}

						<div class="form-group row">
							<label for="address" class="col-md-2 col-form-label text-md-right">Address :</label>

							<div class="col-md-10">
								<input id="address" type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" name="address" value="{{ old('address') ?: $user['address'] }}" required placeholder="Ex: Malang (Min: 3 Character)">

								@if ($errors->has('address'))
									<span class="invalid-feedback" role="alert">
										<strong>{{ $errors->first('address') }}</strong>
									</span>
								@endif

							</div>
						</div>

						<hr>

						<div class="form-group row mb-0">
							<div class="col-md-12 text-center">
								<button type="submit" class="btn btn-success float-left">
									Update
								</button>
								
								<a href="{{ route('user.index') }}" class="btn btn-danger float-right">
									Cancel
								</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(function() {

		// First Focus
		$('input[name=name]').focus();

		$('#btn-reset').click(function() {
			if (confirm('Reset Value to Default (All Changes Will Not Saved) ?')) {
				self.location.reload();
			}	
		});
	});

</script>

@endsection
