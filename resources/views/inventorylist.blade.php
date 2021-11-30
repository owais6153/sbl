@extends('layouts.app')

@section('content')
<style>
	table.dataTable th, table.dataTable td {
    padding: 16px 16px !important;
}
</style>
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
				<a href="{{route('inventoryByBarcode')}}" class="btn btn-primary mb-3 <?= ($filter == 'barcode') ? 'disabled' : '' ; ?>">Show Locations By Barcode</a>
				<a href="{{route('inventory')}}" class="btn btn-primary mb-3 <?= ($filter == 'item') ? 'disabled' : '' ; ?>">Show Locations By Items</a>
			<div class="wc-content-inner">	    
				<div class="custom_data_filter"><label>Search:<input type="search" value="{{ Request::get('search') }}" class="" placeholder="">					  	</label></div>

				<table id="wc-table" class="display">
					<thead>
					    <tr>
					      <th scope="col">Item Name <span id='sortbyitemname' style="float: right"><i class="fas fa-sort"></i></span></th>
					      <th scope="col">Barcode</th>
					      <th scope="col">Locations (QTY)</th>
					      <th scope="col">Total Inventory <span id='sortbytotalinventory' style="float: right"><i class="fas fa-sort"></i></span></th>
					      <th scope="col">OnHand (RidgeField) <span id='sortbyonhand' style="float: right"><i class="fas fa-sort"></i></span></th>
					      <th scope="col">Diff  </th>
					      <th scope="col">Moves</th>
					      <th scope="col"></th>
					    </tr>
					</thead>
					<tbody>
					  	@empty(!$inventories)
					  	@isset($inventories['data'])
					  		@foreach ($inventories['data'] as $index => $inventory)
							    <tr>
							    	<td>
							    		@foreach ($inventory['item'] as $itemindex => $item)
								    		{{$item}} 
								    		@if (isset($inventory['item'][$itemindex + 1]))
								    			,<br>
								    		@endif
							    		@endforeach
							    	</td>
							    	<td>{{$inventory['barcode']}}</td>
							    	<td>
							    		@foreach ($inventory['locations'] as $location)
							    			{{$location['location_name']}} ({{$location['location_sum']}})
							    		@endforeach
							    		@isset($inventory['more'])
							    		    <b onclick="$(`#b{{ $inventory['barcode'] }}i-{{$index}}`).fadeToggle(); $(this).parents('tr').find('.fas').toggleClass('fa-plus-circle'); $(this).parents('tr').find('.fas').toggleClass('fa-minus-circle');">[More...]</b>
							    		@endif
							    		
							    	</td>
							    	<td>{{$inventory['total']}}</td>
							    	<td>{{$inventory['onhand']}}</td>
									@if($inventory['onhand'] > 0 && $inventory['total'] < $inventory['onhand'])
							    		<td>{{ $inventory['onhand'] - $inventory['total']}}</td>
									@else
										<td>0</td>
									@endif
							    	<td>
							    		<a href="{{route('getInventoryDetailsView', ['barcode' => $inventory['barcode']] ) }}">All moves</a><br>
							    	</td>
							    	<td>
	        						    @isset($inventory['locationsData'])
							    		<a href="javascript:void(0)" onclick="$(`#b{{ $inventory['barcode'] }}i-{{$index}}`).fadeToggle(); $(this).find('.fas').toggleClass('fa-plus-circle'); $(this).find('.fas').toggleClass('fa-minus-circle');"><i class="fas fa-plus-circle"></i>
								    	</a>
							    		@endif
							    	</td>
							    </tr>
							    @isset($inventory['locationsData'])
							    <tr>
							    	<td colspan="6" style="padding: 5px !important; display: none;" id="b{{ $inventory['barcode'] }}i-{{$index}}">
							    		<table class="display">
							    			<thead class="bg-dark text-light">
							    				<tr>
							    					<th>Item Name</th>
							    					<th>Barcode</th>
							    					<th>Location</th>
							    					<th>Quantity</th>
							    					<th>Expiration</th>
							    				</tr>
							    			</thead>
							    			<tbody>
							    				@foreach ($inventory['locationsData'] as $locationsData)
								    				<tr>
												    	<td>
												    		@foreach ($inventory['item'] as $itemindex => $item)
													    		{{$item}}
													    		@if (isset($inventory['item'][$itemindex + 1]))
													    			,<br>
													    		@endif
												    		@endforeach
							    						</td>
												    	<td>										@isset($locationsData['barcode'])		    	{{$locationsData['barcode']}}
										@else
        										{{$inventory['barcode']}}
        								@endif</td>
												    	<td>{{$locationsData['name']}}</td>
												    	<td>{{$locationsData['count']}}</td>
												    	<td>{{$locationsData['expiration']}}</td>
								    				</tr>
							    				@endforeach
							    			</tbody>
							    		</table>
							    	</td>
							    </tr>
							    @endif
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
			if ( $('#wc-table table').length > 0) {
			    $('#wc-table table').DataTable({  "paging":   false,"info":     false});

			}
		} );
        var xhrRunning = false;
        var xhr;
		page =1;
		order = "asc";
		route ="{{route('inventory')}}";
		const urlParams = new URLSearchParams(window.location.search);
		if(window.location.href == 'http://127.0.0.1:8000/inventory-by-barcode'){
			route = '{{route('inventoryByBarcode')}}';
		}
		if(document.URL.indexOf("page") >= 0){ 
			page = urlParams.get('page');
		}
		$(document).on('keyup', '.custom_data_filter input' , function(){
		    if(xhrRunning){
                xhr.abort();
		    }
			$('#loader').show();
		    xhrRunning = true;
			xhr = $.ajax({
		         headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	             },
			     url: "{{route('inventory')}}", 
			     type: 'get',
			     data: {'search': $(this).val(), 'output' : 'html'},
			     dataType: 'json',
			     success: function (response) {
			     $('#loader').hide();
			     if (response.status == 'success') {
			        xhrRunning = false;
			     	$('.wc-content-inner').html(response.html);
			     	  $('#wc-table').DataTable({  "paging":   false,"info":     false, searching: false});
			     }
			     if (response.status == '404') {
			     	alert(response.error);
			     }
			     if (response.status == 'error') {
			     	alert(response.error);
			     }
			     },
			     error: function (){
			     	$('#loader').hide();
			     	xhrRunning = false;
			     //	alert("Something Went Wrong...");
			     }
			});
		});
		
		$(document).on('click', '#sortbyitemname' , function(){
			
			
			if(order == "asc"){
				order = "desc";
			}else{
				order = "asc";
			}
		    if(xhrRunning){
                xhr.abort();
		    }
			$('#loader').show();
		    xhrRunning = true;
			xhr = $.ajax({
		         headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	             },
			     url: route, 
			     type: 'get',
			     data: {'sort': 'byItemName', 'output' : 'html',"order":order,'page':page},
			     dataType: 'json',
			     success: function (response) {
					//  console.log(response);
			     $('#loader').hide();
			     if (response.status == 'success') {
			        xhrRunning = false;
					
			     	$('.wc-content-inner').html(response.html);
			     	  $('#wc-table').DataTable({  "paging":   false,"info":     false, searching: false});
			     }
			     if (response.status == '404') {
			     	alert(response.error);
			     }
			     if (response.status == 'error') {
			     	alert(response.error);
			     }
			     },
			     error: function (){
			     	$('#loader').hide();
			     	xhrRunning = false;
			     //	alert("Something Went Wrong...");
			     }
			});
		});
		$(document).on('click', '#sortbytotalinventory' , function(){
			
			
			if(order == "asc"){
				order = "desc";
			}else{
				order = "asc";
			}
		    if(xhrRunning){
                xhr.abort();
		    }
			$('#loader').show();
		    xhrRunning = true;
			xhr = $.ajax({
		         headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	             },
			     url: route, 
			     type: 'get',
			     data: {'sort': 'byTotalInventory', 'output' : 'html',"order":order,'page':page},
			     dataType: 'json',
			     success: function (response) {
					//  console.log(response);
			     $('#loader').hide();
			     if (response.status == 'success') {
			        xhrRunning = false;
					
			     	$('.wc-content-inner').html(response.html);
			     	  $('#wc-table').DataTable({  "paging":   false,"info":     false, searching: false});
			     }
			     if (response.status == '404') {
			     	alert(response.error);
			     }
			     if (response.status == 'error') {
			     	alert(response.error);
			     }
			     },
			     error: function (){
			     	$('#loader').hide();
			     	xhrRunning = false;
			     //	alert("Something Went Wrong...");
			     }
			});
		});
		
		$(document).on('click', '#sortbyonhand' , function(){
			
			if(order == "asc"){
				order = "desc";
			}else{
				order = "asc";
			}
		    if(xhrRunning){
                xhr.abort();
		    }
			$('#loader').show();
		    xhrRunning = true;
			xhr = $.ajax({
		         headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	             },
			     url: route
				 , 
			     type: 'get',
			     data: {'sort': 'byonHand', 'output' : 'html',"order":order,'page':page},
			     dataType: 'json',
			     success: function (response) {
					//  console.log(response);
			     $('#loader').hide();
			     if (response.status == 'success') {
			        xhrRunning = false;
					
			     	$('.wc-content-inner').html(response.html);
			     	  $('#wc-table').DataTable({  "paging":   false,"info":     false, searching: false});
			     }
			     if (response.status == '404') {
			     	alert(response.error);
			     }
			     if (response.status == 'error') {
			     	alert(response.error);
			     }
			     },
			     error: function (){
			     	$('#loader').hide();
			     	xhrRunning = false;
			     //	alert("Something Went Wrong...");
			     }
			});
		});
	</script>
@endsection
