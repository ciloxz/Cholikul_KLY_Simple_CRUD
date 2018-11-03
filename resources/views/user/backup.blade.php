@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-10">
			<div class="card">
				<div class="card-header">

					{{-- Depends on Action (Create / Update) --}}
					<h4 class="title">Form {{ session('update') ? 'Edit' : 'Add' }} User</h4>

					@if (session('update'))
						<button type="button" id="btn-reset" class="btn btn-primary float-right">
							Reset
						</button>
					@else
						<button  type="button" id="btn-clear" class="btn btn-primary float-right">
							Reset
						</button>
					@endif
				</div>

				<div class="card-body">

					<form id="form-user" method="POST" action="{{ route('user.store') }}">
						@csrf
						
						{{-- Name --}}
						<div class="form-group row">
							<label for="name" class="col-md-2 col-form-label text-md-right">Name :</label>

							<div class="col-md-10">
								<input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ isset($user) && count($errors) == 0 ? $user['name'] : old('name') }}" required placeholder="Nick Name (Min : 3 Character)">

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
								<input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ isset($user) && count($errors) == 0 ? $user['email'] : old('email') }}" required placeholder="email@address.com">

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
								<input id="birth" type="date" class="form-control{{ $errors->has('birth') ? ' is-invalid' : '' }}" name="birth" value="{{ isset($user) && count($errors) == 0 ? $user['birth'] : old('birth') }}" required placeholder="Format (mm / dd / YYYY)">

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
								<input id="phone" type="number" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ isset($user) && count($errors) == 0 ? $user['phone'] : old('phone') }}" required placeholder="Ex: 087759625462">

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
								
								  		@if (isset($user['gender']) && $user['gender'] == 'Male' && count($errors) == 0)
								  			checked
								  		@else
								  			{{ old('gender') == 'Male' ? 'checked' : '' }}
								  		@endif

								  	>

								  	<label class="form-check-label" for="male">
								    	Male
								  	</label>
								</div>

								<div class="form-check">
								  	<input class="form-check-input" type="radio" name="gender" id="female" value="Female" 
								  		
								  		@if (isset($user['gender']) && $user['gender'] == 'Female' && count($errors) == 0)
								  			checked	
								  		@else
								  			{{ old('gender') == 'Female' ? 'checked' : '' }}
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
								<input id="address" type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" name="address" value="{{ isset($user) && count($errors) == 0 ? $user['address'] : old('address') }}" required placeholder="Ex: Malang (Min: 3 Character)">

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
									{{ session('update') ? 'Update' : 'Create' }}
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

		// Clear Input
		$('#btn-clear').click(function() {

			if (confirm('Clear All Input Form ?')) {
			
				// Reset Input , Clear Error Border from input
				$('#form-user')
					.find('input[type=text], input[type=email], input[type=date], input[type=number]')
					.val('')
					.removeClass('is-invalid');
				
				// Remove Error Info
				$('#form-user').find('span.invalid-feedback').remove();	

				// Remove Gender Checked , Remove Error Info
				$('#form-user input[type=radio').each(function() {
					$(this).prop('checked', false);
					$(this).parent().siblings('span.invalid-feedback').remove();
				});

				$('input[name=name]').focus();
			}
		});

		$('#btn-reset').click(function() {
			if (confirm('Reset Value to Default (All Changes Will Not Saved) ?')) {
				self.location.reload();
			}	
		});
	});

</script>

@endsection
