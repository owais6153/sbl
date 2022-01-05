@extends('layouts.app')

@section('headermeta')
	<style type="text/css">
		div#available_expiration_wrapper {
		    padding-top: 20px;
		}
	</style>
@endsection

@section('content')

	<div class="col-lg-9 col-md-9">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>Add Inventory Location </h2>
				 @if (Session::get('danger')) 
			    	@foreach (Session::get('danger')[0] as $error)
			    		<div class="alert alert-danger">{{$error}}</div>
			    	@endforeach
			    @endif
			</div>
			<div class="alert"></div>
			<audio id="beep">
			    <source src="{{ asset('audio/beep.mp3') }}" ype="audio/mp3">
			</audio>
			<div class="wc-content">
				<form class="custom_form" action="{{route('addusers')}}" method="POST" enctype="multipart/form-data" id="saveInventory" autocomplete="off">
					   @csrf
					   <div id="fileFields"></div>
					  <div class="form-group col-half">
					    <label for="barcode">Barcode</label>
					    <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Enter Barcode"> <img src="{{asset('images/preloader.gif')}}" id="barcode_loader" style="display: none;">
					    <div id="items"></div>
					  </div>
					  <div class="form-group col-half">
					    <label for="quantity">Quantity</label>
					    <input type="number" class="form-control" min="1" id="quantity" placeholder="Enter Quantity" name="quantity">
					  </div>
					  <input type="hidden" name="from_id" id="from_id" value="">
					  <div class="form-group col-half">
					    <label for="from">From Location</label>		
					    <select class="form-control" id="from" placeholder="From Location" name="from">
					        <option value="">Select From Location</option>
					    	<option value="Receiving">Receiving</option>
							@can('inventory_adjustment')
					    	<option value="Adjustment">Adjustment</option>
							@endcan
					    	<optgroup id="options" label="Locations"></optgroup>
					    </select>
					    <img src="{{asset('images/preloader.gif')}}" id="from_loader" style="display: none;">
					    <span class="w-100" id="from_error" style="color: red; display:none;">Please select the valid from location.</span>
					  </div>
					  <div class="form-group col-half">
					    <label for="to">To Location</label>
					    <input type="text" class="form-control" id="to" placeholder="To Location" name="to">
					  </div>
					  <div class="form-group col-half">
					    <label for="expiration_date">Expiration Date</label>
					    <input type="text" class="form-control" id="expiration_date" placeholder="Expiration Date" name="expiration_date">
					    <select  class="form-control" onchange="setExiprationDateAndQuantity(this)" name="expiration_date" id="expiration_date_select" style="display: none;"></select>
					    <img src="{{asset('images/preloader.gif')}}" id="barcode_loader" style="display: none;">
					  </div>
					  <div class="form-group col-half">
					    <label for="pallet_number">Pallet number</label>
					    <input type="text" class="form-control" id="pallet_number" placeholder="Pallet Number" name="pallet_number">
					  </div>
					  <div class="form-group uploader col-full">
					    
					    <button type="button" id="imageUploader">Upload Images <img src="{{asset('images/upload_img.png')}}"></button>
					    <input type="file" id="fileupload" class="form-control" style="visibility: hidden; opacity: 0;" id="images" name="upimages[]" multiple="">
					    <div id="preview"></div>
					  </div>

					  <div class="btn-form">
					  	<button type="button" id="submitForm" class="btn btn-primary">Submit</button>
					  	<img src="{{asset('images/preloader.gif')}}">
					  </div>
				</form>
			</div>
		</div>
	</div>

@endsection


@section('script')
<script type="text/javascript">
	$('#imageUploader').click(function(){
		$('#fileupload').click();
	});
	$('#fileupload').change(function(){
	

		$('.btn-form img').css("display","inline-block");

		var formData = new FormData();
		let TotalFiles = $('#fileupload')[0].files.length; //Total files
		let files = $('#fileupload')[0];
		for (let i = 0; i < TotalFiles; i++) {
			formData.append('files' + i, files.files[i]);
		}
		formData.append('TotalFiles', TotalFiles);
	    $.ajax({
	         headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
		     url: "{{route('uploadImage')}}", 
		     type: 'post',
		     data: formData,
		     dataType: 'json',
		     contentType: false,
		     processData: false,
		     success: function (response) {
		     	$('.btn-form img').css("display","none");
		       if (response.status == 'success') {
   			       for(var index = 0; index < response.files.length; index++) {
			         var src = "{{ asset('uploads/') }}" + "/" + response.files[index];

			         $('#preview').append('<div><i class="far fa-times-circle" onclick="removeImages(this)" data-id="uImg'+index+'" imgsrc="'+response.files[index]+'"></i><img src="'+src+'" ></div>');
			         

			         $('#fileFields').append('<input id="uImg'+index+'" type="hidden" name="images[]" value="'+response.files[index]+'" >');
			       }
		       }
		       else{
			       	alert(response.error);
		       }

		     },
		     error: function (){
		     	$('.btn-form img').css("display","inline-block");
		     	alert("Something Went Wrong...");
		     }
		});
	})

	function removeImages(elem){
		$('.btn-form img').css("display","inline-block");
		var path = $(elem).attr('imgsrc');
		$.ajax({
	         headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	         },
		     url: "{{route('removeImage')}}", 
		     type: 'POST',
		     data: {'removepath': path},
		     dataType: 'json',
		     success: function (response) {
		     	$('.btn-form img').css("display","none");
		        if (response.status == 'success') {
		       		$(elem).parent('div').remove();
		       		$('#' +$(elem).attr('data-id')).remove();
		        }
		        else{
			       	alert(response.error);
		        }
		     },
		     error: function (){
		     	$('.btn-form img').css("display","inline-block");
		     	alert("Something Went Wrong...");
		     }
		});
	}

	$('#saveInventory').submit( function (e){
		e.preventDefault();
	})
	@cannot('inventory_adjustment')
	$('#to').keyup(function(){
	if($(this).val() == "Adjustment" || $(this).val() == "adjustment"){
		alert('You Are Forbidden to make adjustments');
		$('#submitForm').attr("disabled", true);
	}else{
		$('#submitForm').attr("disabled", false);

	}
	});
	@endcannot
	$('#submitForm').click( function (e){
		var max = $('#quantity').attr('max');
		let flag = true;
		
		if($('#from').val() == 'Adjustment' || $('input#to').val().toLowerCase() == 'adjustment'){
            flag = confirm('Are you sure you want to do Adjustment?');
        }
        if(parseInt($('#quantity').val()) > 5000) {
            alert("You can not move more then 5000 Items.");
            flag = false;
        }
        if (flag && typeof max !== 'undefined' && max !== false) {
            if(parseInt(max) < parseInt($('#quantity').val())){
                alert("You can not move more then " + max + " Items.");
                flag = false;
            }
        }
        if(flag && $('#from').val() != 'Receiving' && $('#from').val() != 'Adjustment'){
            if($('select#expiration_date_select').val() == ''){
                alert('Please select expiration date.');
                flag = false;
            }            
        }
        if(flag && $('#from').val() == $('input#to').val()){
            alert("You can't put same values in both locations fields.");
            flag = false;
        }
        // if(flag && $('#from').val() == 'Receiving' && ($('input#to').val().toLowerCase() == 'shipping' || $('input#to').val().toLowerCase() == 'production')){
        //     alert("You can't do shipping while receiving");
        //     flag = false;
        // }
        
        if(flag && $('#from').val() == 'Receiving' && $('input#to').val().toLowerCase() == 'adjustment'){
            alert("You can't do adjustment while receiving");
            flag = false;
        }
        
        if(flag && $('#from').val() == 'Adjustment' && ($('input#to').val().toLowerCase() == 'shipping' || $('input#to').val().toLowerCase() == 'production')){
            alert("You can't do adjustment while shipping");
            flag = false;
        }
        
        if(flag == true){
    		$('.btn-form img').css("display","inline-block");
    		var forms= document.getElementById('saveInventory');
    		var formData = new FormData(forms);
    		$.ajax({
    	         headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
    		     url: "{{route('saveInventory')}}", 
    		     type: 'post',
    		     data: formData,
    		     dataType: 'json',
    		     contentType: false,
    		     processData: false,
    		     success: function (response) {
    		     	$('.btn-form img').css("display","none");
    		       if (response.status == 'success') {
    		       		$('.alert').text(response.success);
    		       		$('.alert').addClass('alert-success');
    		       		$('#saveInventory').trigger("reset");
    			       	$('#preview').html('');			         
    			        $('#fileFields').html('');
    			        $('#items').html('');
                		$('#options').html('');
                		$('#expiration_date_select').html('');
                		$('#expiration_date').removeAttr('disabled');
                		$('#expiration_date').removeAttr('required');
                		$('#expiration_date').val('');
                		$('#expiration_date').show();
                		$('#expiration_date_select').hide();
                		$('#quantity').attr('max', '');
                		$('#from').select2();
    		       }
    		       else{
    			       	alert(response.error);
    		       }
    
    		     },
    		     error: function (){
    		     	$('.btn-form img').css("display","none");
    		     	alert("Something Went Wrong...");
    		     }
    		});
	    }
	})
	var xhrrunning = false;
	var xhr;
	$('#barcode').keyup(function(){
	    if(xhrrunning){
	        xhr.abort();
	    }
		$('#items').html('');
		if ($(this).val() != '') {
	    	$('#barcode_loader').show();
			let barcode = $(this).val() ;
			xhrrunning = true;
			xhr = $.ajax({
		         headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	             },
			     url: "{{route('getlocationbybarcode')}}", 
			     type: 'post',
			     data: {'barcode': barcode},
			     dataType: 'json',
			     success: function (response) {
                    xhrrunning = false;
					$('#barcode_loader').hide();
			       if (response.status == 'error') {
					alert(response.error);
			       }
			       if(response.items.length > 0){
			       	let item = '';
			       	for(var index = 0; index < response.items.length; index++) {
				       	item += '<span>'+response.items[index][`item_number`]+'</span>';
				       	item += '<input type="hidden" name="item_id" value="'+response.items[index][`id`]+'" class="item_id"><br>';
					}	
					$('#items').append($(item));
			       }

			     },
			     error: function (){
                    xhrrunning = false;
					$('#barcode_loader').hide();
			     //	alert("Something Went Wrong...");
			     }
			});
		}
	})

	$('#barcode').change(function(){
		$('#options').html('');
		$('#from_id').val();
		$('#expiration_date_select').html('');
		$('#expiration_date').removeAttr('disabled');
		$('#expiration_date').removeAttr('required');
		$('#expiration_date').val('');
		$('#quantity').attr('max', '');
		$('#expiration_date').val('');
		$('#expiration_date').show();
		$('#expiration_date_select').hide();
        $('#from').select2();
		if ($(this).val() != '') {
	    	$('#barcode_loader').show();
    		$('#from_loader').show();
			let barcode = $(this).val() ;
			xhrrunning = true;
			xhr = $.ajax({
		         headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	             },
			     url: "{{route('getlocationbybarcode')}}", 
			     type: 'post',
			     data: {'barcode': barcode},
			     dataType: 'json',
			     success: function (response) {
					$('#barcode_loader').hide();
					$('#from_loader').hide();
			       if (response.status == 'success') {
			       	   let html = '';
			       	   if (response.locations.length == undefined && response.locations[1] != '') {
			       	   		html+='<option value="'+response.locations[1]+'">' + response.locations[1] + '</option>';
			       	   }
			       	   else{
				       	   for(var index = 0; index < response.locations.length; index++) {
				       	   		html+= '<option value="'+response.locations[index]+'">' + response.locations[index] + '</option>';
					       }			       	   	
			       	   }
				       $('#options').html(html); 
				       $('#from').select2();
			       }
			       else if (response.status == 'error') {
			       	$('#from').select2();
			       	$('#barcode_loader').hide();
					$('#from_loader').hide();
					alert(response.error);
			       }

			       if (response.action == 'append') {
			       	 $('#barcode').val(response.barcode);
			       }

			     },
			     error: function (){
					$('#barcode_loader').hide();
					$('#from_loader').hide();
			     //	alert("Something Went Wrong...");
			     }
			});
		}
	})
	function setExiprationDateAndQuantity(elem) {
		$('#quantity').attr('max', $(elem).find('option:selected').attr('data-count'));
		$('#from_id').val($(elem).find('option:selected').attr('data-fromid'));
	}
	$(document).ready(function() {
	    $('#from').select2();
	    $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
		  $(this).closest(".select2-container").siblings('select:enabled').select2('open');
		})
		let lastHoverItem = '';
		let lastSearch = '';
		$(document).on('keydown', 'input.select2-search__field', function(e){

		    if(e.shiftKey && e.keyCode == 9) { 
		    	$('#from').val(lastHoverItem).trigger("change");
		    	if($('#from').val() == ''){
		    	    $('#beep').get(0).play();
		    	    $('#from_error').fadeIn();
    		    }
    		    else if($('#from').val().toLowerCase().search(lastSearch) == -1){
    		        $('#from').val('').trigger("change");
		    	    $('#beep').get(0).play();
		    	    $('#from_error').fadeIn();
    		    }
    		    else{
    		        $('#quantity').focus();
    		    }
			}
		    else if (e.keyCode == 9) {
		    	$('#from').val(lastHoverItem).trigger("change");
		    	if($('#from').val() == ''){
		    	    $('#beep').get(0).play();
		    	    $('#from_error').fadeIn();
    		    }
    		    else if(  $('#from').val().toLowerCase().search(lastSearch) == -1) {
    		        $('#from').val('').trigger("change");
		    	    $('#beep').get(0).play();
		    	    $('#from_error').fadeIn();
    		        
    		    }
    		    else{
    		        $('#to').focus();
    		    }
		    }
		    else{
		    	let temp;
		    	if ($('span.select2-selection.select2-selection--single').attr('aria-activedescendant') != '' && $('span.select2-selection.select2-selection--single').attr('aria-activedescendant') != undefined) {	
			    	temp = $('span.select2-selection.select2-selection--single').attr('aria-activedescendant').split("-");
			    	lastHoverItem = temp.at(-1);
			    	lastSearch = $('input.select2-search__field').val();
			    	if (lastSearch != '') {
			    		lastSearch = lastSearch.toLowerCase();
			    	}
			    	$('#from_error').fadeOut();
		    	}
		    }
		})
		$('#from').change(function(){
		    if($('#from').val() == ''){
		   	    $('#beep').get(0).play();
		   	    $('#from_error').fadeIn();
    		}
    		else{
		    	$('#from_error').fadeOut();
    		}
    		$('#expiration_date_select').html('');
    		$('#from_id').val();
    		$('#expiration_date').removeAttr('disabled');
    		$('#expiration_date').removeAttr('required');
    		$('#expiration_date').val('');
    		$('#expiration_date').show();
    		$('#expiration_date_select').hide(); 
    		$('#quantity').attr('max', '');
    		if ($(this).val() != '' && $(this).val() != 'Receiving' && $(this).val() != 'Adjustment' && $('#barcode').val() != '' ) {
    		    $('#from_loader').show();
    			let from = $(this).val() ;
    			$.ajax({
    		         headers: {
    	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    	             },
    			     url: "{{route('getExiprationDateAndQuantity')}}", 
    			     type: 'post',
    			     data: {'from': from, 'barcode':  $('#barcode').val()},
    			     dataType: 'json',
    			     success: function (response) {
    			       $('#from_loader').hide();
    			       if (response.status == 'success') {
    			       	let html = '<option value="">Select expiration date</option>';
    			       	 if (response.data.length == undefined && response.data[1] != '') {
    			       	  	let date = (response.data[1][`expiration`] == null) ? 'None' : response.data[1][`expiration`];
    			       	   	html+= '<option value="'+response.data[1][`expiration`]+'" data-count="'+response.data[1][`count`]+'" data-fromid="'+response.data[1][`from_id`]+'">' + date + '</option>';
    
    			       	 }
    			       	 else{
    			       	   for(var index = 0; index < response.data.length; index++) {
    			       	  	let date = (response.data[index][`expiration`] == null) ? 'None' : response.data[index][`expiration`];
    			       	  	let attri = (response.data.length == 1) ? 'selected' : '';
    			       	   	html+= '<option '+attri+' value="'+response.data[index][`expiration`]+'" data-count="'+response.data[index][`count`]+'" data-fromid="'+response.data[index][`from_id`]+'">' + date + '</option>';
    
    
    				       }
    
    				        $('#expiration_date_select').attr('required', 'required');
    			       	 }
    
    
    					    $('#expiration_date').hide();
    				        $('#expiration_date_select').html(html); 
    				        $('#expiration_date_select').show(); 
    				       	if(response.data.length == 1){
    			       	   		$('#quantity').attr('max', $('#expiration_date_select').find('option:selected').attr('data-count'));
    							$('#from_id').val($('#expiration_date_select').find('option:selected').attr('data-fromid'));
    			       	   	}
    
    
    			       }
    
    			       else{
    			       		$('#from').select2();
    			       		$('#barcode_loader').hide();
    						$('#from_loader').hide();
    						alert(response.error);
    			       }
    			     },
    			     error: function (){
    			        $('#from_loader').hide();
    			     	alert("Something Went Wrong...");
    			     }
    			});
    		}
    	})
	});
</script>
@endsection