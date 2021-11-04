@extends('layouts.app')

@section('content')

	<div class="col-lg-9 col-md-9">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>All Inventories</h2>
				@if (session('success'))
				    <div class="alert alert-success mb-3">
				        {{ session('success') }}
				    </div>
				@endif
			</div>
			<div class="wc-content">
				<table id="wc-table" class="table table-bordered table-striped display">
					<thead>
					    <tr>
					      <th scope="col">#</th>
					      <th scope="col">Barcode</th>
					      <th scope="col">From Location</th>
					      <th scope="col">To Location</th>
					      <th scope="col">Qty</th>
					  	  <th scope="col">Email</th>
					      <th scope="col">Expiration</th>
					      <th scope="col">Pallet</th>
					      <th scope="col">Images</th>
					      <th scope="col">Date created</th>
					      <th scope="col">Time created</th>
					    </tr>
					</thead>

				</table>
			</div>
		</div>
	</div>


@endsection

@section('script')
	<script type="text/javascript">
		$('.deleteIt').on('click', function(e){
			e.preventDefault();
			let result = confirm('Are you sure you want to delete?');
			if (result) {
				window.location.href = $(this).attr('href');
			}
		})
		  $('#wc-table').DataTable({
		         processing: true,
		         serverSide: true,
		         ajax: {
		          url: "{{ route('getInventoryDetails', ['id' => request('id')]) }}",
		          type: 'GET',
		         },

		         columns: [
		                  { data: 'id', name: 'id', 'visible': false},
		                  { data: 'barcode', name: 'barcode' },
		                  { data: 'from', name: 'from' },
		                  { data: 'to', name: 'to' },
		                  { data: 'quantity', name: 'quantity' },
		                  { data: 'email', name: 'users.email' },
		                  { data: 'expiration_date', name: 'expiration_date' },
		                  { data: 'pallet_number', name: 'pallet_number' },
		                  { data: 'images_links', 
		                      name: 'images_links', 
		                      orderable: true, 
		                      searchable: true
		                  },
		                  { data: 'date', 
		                      name: 'date', 
		                      orderable: true, 
		                      searchable: true
		                  },
		                  { data: 'time', 
		                      name: 'time', 
		                      orderable: true, 
		                      searchable: true
		                  }
		               ],
		        order: [[0, 'desc']]
		  });
		  



	</script>
@endsection
