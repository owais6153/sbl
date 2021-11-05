@extends('layouts.app')

@section('content')

	<div class="col-lg-9 col-md-9">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>All Inventories</h2><br>
			</div>
			@if (session('success'))
			    <div class="alert alert-success mb-3">
			        {{ session('success') }}
			    </div>
			@endif
			@if (session('danger'))
			    <div class="alert alert-danger mb-3">
			        {{ session('danger') }}
			    </div>
			@endif
			<div class="wc-content">
				<form method="get" class="row mb-4">
					<div class="col-md-2 pr-1">
						<select id="trash" name="trash" class="form-control">
							<option value="0">Active</option>
							<option {{ (request('trash') == 1) ? 'selected' : '' }} value="1">Trashed</option>
						</select>
					</div>
					<div class="col-md-1 pl-0">
						<button class="btn btn-info btn-block">Filter</button>
					</div>				
				</form>
				
				<table id="wc-table" class="table table-bordered table-striped display">
					<thead>
					    <tr>
					      <th scope="col">#</th>
					      <th scope="col">Barcode</th>
					      <th scope="col">From</th>
					      <th scope="col">To</th>
					      <th scope="col">Qty</th>
					  	  <th scope="col">Email</th>
					      <th scope="col">Expiration</th>
					      <th scope="col">Pallet</th>
					      <th scope="col">Images</th>
					      <th scope="col">Time created</th>
					      <th scope="col">Action</th>
					    </tr>
					</thead>

				</table>
			</div>
		</div>
	</div>


@endsection

@section('script')
	<script type="text/javascript">
		$(document).on('click', '.deleteIt', function(e){
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
		          url: "{{ route('getInventoryDetails', ['barcode' => request('barcode')]) }}",
		          type: 'GET',
		          data: function (d) {
			          d.trash = $('#trash').val();
			       }
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
		                  { data: 'time', 
		                      name: 'time', 
		                      orderable: true, 
		                      searchable: true
		                  },
		                  { data: 'actions', 
		                      name: 'actions', 
		                      orderable: true, 
		                      searchable: true
		                  }
		               ],
		        order: [[0, 'desc']]
		  });
		  



	</script>
@endsection
