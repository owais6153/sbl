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
			<img src="{{asset('images/preloader.gif')}}" id="loader" style="display: none;">
			<div class="wc-content">			    
				<div class="custom_data_filter"><label>Search:<input type="search" value="{{ Request::get('search') }}" class="" placeholder="">					  	</label></div>

				<table id="wc-table" class="display">
					<thead>
					    <tr>
					      <th scope="col">Barcode</th>
					      <th scope="col">Locations (QTY)</th>
					      <th scope="col">Total Inventory</th>
					      <th scope="col">Moves</th>
					    </tr>
					</thead>
					<tbody>
					  	@empty(!$inventories)
					  	@isset($inventories['data'])
					  		@foreach ($inventories['data'] as $index => $inventory)
							    <tr>
							    	<td>{{$inventory['barcode']}}</td>
							    	<td>
							    		@foreach ($inventory['locations'] as $location)
							    			{{$location['location_name']}} ({{$location['location_sum']}})
							    		@endforeach
							    	</td>
							    	<td>{{$inventory['total']}}</td>
							    	<td>
							    		<a href="{{route('getInventoryDetailsView', ['id' => $inventory['barcode']] ) }}">Click for all moves</a><br>
							    	</td>
							    </tr>
							@endforeach
							@endif
					    @else
					    	<td colspan="4">No user found</td>	
					    @endif
					    
					</tbody>
				</table>
				{{$inventories['links']}}
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
		$(document).ready( function () {
			if ( $('#wc-table').length > 0) {
			    $('#wc-table').DataTable({  "paging":   false,"info":     false, searching: false});
			}
		} );

		$(document).on('keyup', '.custom_data_filter input' , function(){
			$('#loader').show();
			$.ajax({
		         headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	             },
			     url: "{{route('listsearch')}}", 
			     type: 'post',
			     data: {'search': $(this).val()},
			     dataType: 'json',
			     success: function (response) {
			     $('#loader').hide();
			     if (response.status == 'success') {
			     	$('.wc-content').html(response.html);
			     	  $('#wc-table').DataTable({  "paging":   false,"info":     false, searching: false});
			     }
			     if (response.status == '404') {
			     	alert(response.error);
			     }

			     },
			     error: function (){
			     	$('#loader').hide();
			     	alert("Something Went Wrong...");
			     }
			});
		});

	</script>
@endsection
