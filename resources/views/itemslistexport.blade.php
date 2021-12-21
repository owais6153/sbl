@extends('layouts.app')

@section('content')

	<div class="col-lg-9 col-md-8">
		<div class="wrap-content">
			<div class="wc-title d-flex justify-content-between">
				<h2>All Items</h2>
				@if (session('success'))
				    <div class="alert alert-success mb-3">
				        {{ session('success') }}
				    </div>
				@endif
				<a href="{{route('csvItemsexport')}}" class="btn btn-primary pull-right">Export</a>
			</div>
			<img src="{{asset('images/preloader.gif')}}" id="loader" style="display: none;">
			<div class="wc-content">
				
				<table id="wc-table" class="display">
					<thead>
					    <tr>
					      <th scope="col">ID</th>
					      <th scope="col">Item Number</th>
					      <th scope="col">Barcode</th>
					      <th scope="col">Total</th>
					      <th scope="col">OnHAND</th>
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
		          url: "{{ route('getItemsexport') }}",
		          type: 'GET',
		         },
		         columns: [
		                  { data: 'id', name: 'item.id', 'visible': false},
		                  { data: 'item_number', name: 'item_number' },
		                  { data: 'productIdentifier', name: 'item_identifiers.productIdentifier' },
		                  { data: 'totalqty', name: 'totalqty' },
		                  { data: 'ridgefield_onhand', name: 'ridgefield_onhand' },
		               ],
		        
		  });
		  
		});
		
	</script>
@endsection