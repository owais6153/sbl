		    
				<div class="custom_data_filter"><label>Search:<input type="search" value="{{$search}}" class="" placeholder=""></label></div>

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
@empty(!$search)
<script type="text/javascript">
	if ($('nav.flex.items-center.justify-between a').length >0) {
		$('nav.flex.items-center.justify-between a').each(function(index, item){
			$(item).attr('href', $(item).attr('href') + '&search=' + $('.custom_data_filter input').val())
		})
	}
</script>
@endif