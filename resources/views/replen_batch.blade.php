@extends('layouts.app')

@section('content')

	<div class="col-lg-9 col-md-8">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>All Replen Batch</h2>
			</div>
			<div class="wc-content">
				<table id="wc-table" class="display">
					<thead>
					    <tr>
					      <th scope="col">ID</th>
					      <th scope="col">Time</th>
					      <th scope="col">Status</th>
					      <th scope="col">Actions</th>
					    </tr>
					</thead>
				</table>
			</div>
		</div>
	</div>


@endsection

@section('script')
	<script type="text/javascript">
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
		          url: "{{ route('getReplenBatch') }}",
		          type: 'GET',
		         },
		         columns: [
		                  { data: 'id', name: 'id', 'visible': false},
		                  { data: 'time', name: 'time' },
		                  { data: 'status', name: 'status' },
		                  { data: 'actions', name: 'actions' },
		               ],
		        order: [[0, 'desc']]
		  });
		  
		});
	</script>
@endsection
