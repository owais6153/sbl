@extends('layouts.app')

@section('content')

	<div class="col-lg-9 col-md-8">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>All Moves</h2>
			</div>
			<div class="wc-content">
				<form class="row mb-4 filters">
					<div class="col-md-3">
						<label>Users</label>
						<select class="form-control" name="user_filter" id="user_filter">
							<option value="">Users</option>
							@foreach ($users as $user)
								<option <?= (isset($_GET['user_filter']) && !empty($_GET['user_filter']) && $_GET['user_filter'] == $user->id) ? 'selected' : '' ;?> value="{{$user->id}}">{{$user->name}} ({{$user->email}})</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3">
						<label>Starting From</label>
						<input type="date" name="start_date"  id="start_date" class="form-control
						" <?= (isset($_GET['start_date']) && !empty($_GET['start_date'])) ? 'value="' . $_GET['start_date'] . '"' : '' ; ?>>
					</div>
					<div class="col-md-3">
						<label>Ending From</label>
						<input type="date" name="end_date"  id="end_date" class="form-control
						" <?= (isset($_GET['end_date']) && !empty($_GET['end_date'])) ? 'value="' . $_GET['end_date'] . '"' : '' ; ?>>
					</div>
					<div class="col-md-3" style="padding-top: 30px;">
						<button type="submit" class="btn btn-block btn-info">Filter</button>
					</div>					
				</form>
				<table id="wc-table" class="display">
					<thead>
					    <tr>
					      <th scope="col">ID</th>
					      <th scope="col">User</th>
					      <th scope="col">Item Number</th>
					      <th scope="col">Barcode</th>
					      <th scope="col">Qty</th>
					      <th scope="col">From</th>
					      <th scope="col">To</th>
					      <th scope="col">Expiration</th>
					      <th scope="col">Created At</th>
					    </tr>
					</thead>
				</table>
			</div>
		</div>
	</div>


@endsection

@section('script')
	<script type="text/javascript">

		$('.filters button[type="submit"]').click(function (event) {
			if (($('#start_date').val() == '' && $('#end_date').val() != '') || ($('#start_date').val() != '' && $('#end_date').val() == '')) {
				event.preventDefault();
				alert('Both Date Range Fields Are Required');
			}
		})

		$(document).ready( function () {
		   $.ajaxSetup({
		      headers: {
		          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		      }
		  });
		 
		  $('#wc-table').DataTable({
		         processing: true,
		         serverSide: true,
		         ajax: {
		          url: "{{ route('getAllMovesData') }}",
		          type: 'GET',
		          data: function (d) {
			          <?php 
			          	echo (isset($_GET['user_filter']) && !empty($_GET['user_filter'])) ? 'd.user = "' . $_GET['user_filter'] . '";' : '';
			          	echo (isset($_GET['start_date']) && !empty($_GET['start_date'])) ? 'd.start_date = "' . $_GET['start_date'] . '";' : '';
			          	echo (isset($_GET['end_date']) && !empty($_GET['end_date'])) ? 'd.end_date = "' . $_GET['end_date'] . '";' : '';
			          ?>
			      }
		         },
		         columns: [
		                  { data: 'id', name: 'id', 'visible': false},
		                  { data: 'name', name: 'users.name' },
		                  { data: 'item_number', name: 'item_number' },
		                  { data: 'barcode', name: 'barcode' },
		                  { data: 'quantity', name: 'quantity' },
		                  { data: 'from', name: 'from' },
		                  { data: 'to', name: 'to' },
		                  { data: 'expiration_date', name: 'expiration_date' },
		                  { data: 'time', name: 'created_at' },
		               ],
		        order: [[7, 'desc']]
		  });
		  
		});
	</script>
@endsection
