		    
				<div class="custom_data_filter"><label>Search:<input type="search" value="{{$search}}" class="" placeholder=""></label></div>

				<table id="wc-table" class="display">
					<thead>
					    <tr>
					      <th scope="col">Item Name</th>
					      <th scope="col">Barcode</th>
					      <th scope="col">Locations (QTY)</th>
					      <th scope="col">Total Inventory</th>
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
							    	<td>
							    		
							    		<a href="{{route('getInventoryDetailsView', ['barcode' => $inventory['barcode']] ) }}">All moves</a><br>
							    	</td>
							    	<td>
							    	     @isset($inventory['locationsData'])
							    		<a href="javascript:void(0)" onclick="$(`#b{{ $inventory['barcode'] }}i-{{$index}}`).fadeToggle();$(this).find('.fas').toggleClass('fa-plus-circle'); $(this).find('.fas').toggleClass('fa-minus-circle');"><i class="fas fa-plus-circle"></i>
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
@empty(!$search)
<script type="text/javascript">
	if ($('nav.flex.items-center.justify-between a').length >0) {
		$('nav.flex.items-center.justify-between a').each(function(index, item){
			$(item).attr('href', $(item).attr('href') + '&search=' + $('.custom_data_filter input').val())
		})
	}
	var fieldInput = $('.custom_data_filter input');
	var fldLength= fieldInput.val().length;
	fieldInput.focus();
	fieldInput[0].setSelectionRange(fldLength, fldLength);


</script>
@endif