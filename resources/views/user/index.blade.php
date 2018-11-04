@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<h4 class="title">User Lists</h4>
					
					{{-- Reset --}}
					<button id="btn-reset" class="btn btn-secondary float-right">
						Reset Data
					</button>

					<form id="form-reset" action="{{ route('user.reset') }}" method="POST" style="display: none">
						@csrf
					</form>

					{{-- Restore --}}
					<button id="btn-restore" class="btn btn-dark float-right">
						Restore Data
					</button>

					<form id="form-restore" method="post" action="{{ route('user.restore') }}" enctype="multipart/form-data" 
						style="display: none">
						<input type="file" name="upload" id="upload" accept=".zip">
						@csrf
					</form>

					{{-- Backup --}}	
					<button id="btn-backup" class="btn btn-info float-right" style="color: white">
						Backup Data
					</button>

					<form id="form-backup" method="post" action="{{ route('user.backup') }}" style="display: none">
						@csrf
					</form>
				</div>

				<div class="card-body">

					{{-- Alert Info --}}
					@if (session('info'))
						<div class="alert alert-info">
							<b>Info : </b>{{ session('info') }}
						</div>
					@endif
					
					{{-- Add --}}
					<a href="{{ route('user.create') }}" class="btn btn-success btn-block">Add</a>
					
					{{-- Search Query --}}
					<form action="{{ route('user.index') }}" id="form-search">
						<input type="text" value="{{ request('search') }}" name="search" placeholder="Type Keyword and Hit Enter to Search ..." 
						class="form-control" id="search">
						<a href="{{ route('user.index') }}" class="btn" id="btn-clear-filter">Reset Search</a>
					</form>

					{{-- User List --}}
					<table class="table table-striped table-bordered table-responsive">
						<tr>
							<th>Nama</th>
							<th>Email</th>
							<th>Date&nbsp;of&nbsp;Birth</th>
							<th>Phone</th>
							<th>Gender</th>
							<th>Address</th>
							<th class="text-center" colspan="2">Action</th>
						</tr>
						@if (count($users) > 0)
							@foreach ($users as $user)
								<tr>
									<td>{{ $user['name'] }}</td>
									<td>{{ $user['email'] }}</td>
									<td>{{ $user['birth'] }}</td>
									<td>{{ $user['phone'] }}</td>
									<td>{{ $user['gender'] }}</td>
									<td>{{ $user['address'] }}</td>

									{{-- Edit --}}
									<td>
										<a href="{{ route('user.edit', ['filename' => $user['key']]) }}" 
											class="btn btn-sm btn-primary">Edit
										</a>
									</td>

									{{-- Delete --}}
									<td>
										<button class="btn btn-sm btn-danger btn-delete" data="{{ $user['name'] }}">Delete</button>

										<form class="form-delete" action="{{ route('user.delete', ['filename' => $user['key']]) }}" method="POST" style="display: none;">
	                                        @csrf
	                                        @method('DELETE')
	                                    </form>
									</td>
								</tr>
							@endforeach
						@else
							{{-- If No Data --}}
							<tr class="text-center">
								<td width="100%" colspan="7">No User Data To Display</td>
							</tr>
						@endif
					</table>
					
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(function() {

		// autofocus to search
		$('#search').focus().select();

		// Delete User
		$('.btn-delete').click(function() {
			
			if (confirm("Delete " + $(this).attr('data') + ' ?')) {
				$(this).next('.form-delete').submit();
			}
		});

		// Backup Data
		$('#btn-backup').click(function() {

			// Run Only When Data Exists
			if ($('table tr').eq(1).text().trim() != 'No User Data To Display') {
				$('#form-backup').submit();
			}else{
				alert('No User Data to Backup')
			}
			
		});

		// Restore Data => Choose File
		$('#btn-restore').click(function() {
			$('#upload').trigger('click');
		});

		// Run Restore
		$('#upload').change(function() {
			
			// Run Only When upload Exists
			if ($(this).val()) {
				if (confirm('Restore Data ? \n[Ok] Run Restore \n[Cancel] Abort Process / Change Restore File')) {
					$('#form-restore').submit();
				}else{
					$('#upload').val('');
				}
			}
		});

		// Reset Data
		$('#btn-reset').click(function() {
			
			// Run Only When Data Exists
			if ($('table tr').eq(1).text().trim() != 'No User Data To Display') {

				if (confirm('Reset Data ? \n (This Process Will Delete All User Data)')) {
					$('#form-reset').submit();
				}

			}else{
				alert('No User Data to Reset')
			}
		});

	});

</script>

@endsection
