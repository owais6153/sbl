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
					      <th scope="col">Item Name</th>
					      <th scope="col">Urlid</th>
					      <th scope="col">Store SKU</th>
					      <th scope="col">Store</th>
					      <th scope="col">30 Days Sale</th>
					      <th scope="col">Amazon Inventory</th>
					      <th scope="col">Unsellable</th>
					      <th scope="col">On Hand</th>
					      <th scope="col">Amount To Replen</th>
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
		          url: "{{ route('getReplenDetail', ['id' => request('id')]) }}",
		          type: 'GET',
		         },
		         columns: [
		                  { data: 'id', name: 'id', 'visible': false},
		                  { data: 'item_name', name: 'item_name' },
		                  { data: 'urlid', name: 'urlid' },
		                  { data: 'store_sku', name: 'store_sku' },
		                  { data: 'store', name: 'store' },
		                  { data: 'days_30_sales', name: 'days_30_sales' },
		                  { data: 'amazon_inventory', name: 'amazon_inventory' },
		                  { data: 'unsellable', name: 'unsellable' },
		                  { data: 'on_hand_ridgefield', name: 'on_hand_ridgefield' },
		                  { data: 'amount_to_replen', name: 'amount_to_replen' },
		               ],
		        order: [[0, 'desc']]
		  });
		  
		});
	</script>
@endsection
