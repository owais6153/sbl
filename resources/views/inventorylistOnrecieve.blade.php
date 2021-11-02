@extends('layouts.app')


@section('content')
	<div class="col-lg-9 col-md-9">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>All On Recieve Inventories</h2>
				@if (session('success'))
				    <div class="alert alert-success mb-3">
				        {{ session('success') }}
				    </div>
				@endif
			</div>
			<div class="wc-content">
				<table class="table table-bordered table-striped display inventory-table" id="wc-table">
			       <thead>
			          <tr>
			             <th>ID</th>
			             <th>Brand</th>
			             <th>Item Number</th>
			             <th>Item Name</th>
			             <th>Warehouse</th>
			             <th>On Hand</th>
			             <th>Available</th>
			             <th>Reserved</th>
			             <th>In Transit</th>
			             <th>On Sales Order</th>
			             <th>On Purchase Order</th>
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
		          url: "{{ route('getOnHandList') }}",
		          type: 'GET',
		          data: function (d) {
		          d.start_date = $('#start_date').val();
		          d.end_date = $('#end_date').val();
		          }
		         },
		         columns: [
		                  { data: 'id', name: 'id', 'visible': false},
		                  { data: 'brand', name: 'brand' },
		                  { data: 'item_number', name: 'item_number' },
		                  { data: 'item_name', name: 'item_name' },
		                  { data: 'warehouse', name: 'warehouse' },
		                  { data: 'on_hand', name: 'on_hand' },
		                  { data: 'available', name: 'available' },
		                  { data: 'reserved', name: 'reserved' },
		                  { data: 'in_transit', name: 'in_transit' },
		                  { data: 'on_sales_order', name: 'on_sales_order' },
		                  { data: 'on_purchase_order', name: 'on_purchase_order' }
		               ],
		        order: [[0, 'desc']]
		  });
		  
		});
		 
		$('#btnFiterSubmitSearch').click(function(){
		     $('#wc-table').DataTable().draw(true);
		}); 
	</script>
@endsection
