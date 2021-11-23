@extends('layouts.app')

@section('content')

	<div class="col-lg-9 col-md-8">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>All Dupliacation in Items</h2>
			</div>
			<div class="wc-content">
				<table id="wc-table" class="display">
					<thead>
					    <tr>
					      <th scope="col">ID</th>
					      <th scope="col">Product Identifier</th>
					      <th scope="col">Item Number</th>
					      <th scope="col">Duplicate Item Number</th>
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
		$(document).ready( function () {
		   $.ajaxSetup({
		      headers: {
		          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		      }
		  });
		 
		  $('#wc-table').DataTable({
		  		 searching: false,
		  		 ordering: false,
		         processing: true,
		         serverSide: true,
		         ajax: {
		          url: "{{ route('getSkippedItems') }}",
		          type: 'GET',
		         },
		         columns: [
		                  { data: 'id', name: 'id', 'visible': false},
		                  { data: 'barcode', name: 'barcode' },
		                  { data: 'item', name: 'item.item_number' },
		                  { data: 'duplicate_item', name: 'item.item_number' },
		                  { data: 'created_at', name: 'created_at' },
		               ],
		        order: [[0, 'asc']]
		  });
		  
		});
	</script>
@endsection
