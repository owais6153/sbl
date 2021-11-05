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
			<div class="wc-content">
				<form class="custom_form" action="{{route('addusers')}}" method="POST" enctype="multipart/form-data" id="saveInventory" autocomplete="off">
					   @csrf
					   <div id="fileFields"></div>
					  <div class="form-group col-half">
					    <label for="barcode">Barcode</label>
					    <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Enter Barcode"> <img src="{{asset('images/preloader.gif')}}" id="barcode_loader" style="display: none;">
					  </div>
					  <div class="form-group col-half">
					    <label for="quantity">Quantity</label>
					    <input type="number" class="form-control" min="1" id="quantity" placeholder="Enter Quantity" name="quantity">
					  </div>
					  <div class="form-group col-half">
					    <label for="from">From Location</label>		
					    <select class="form-control" id="from" placeholder="From Location" name="from">
					    	
					    	<option value="Receiving">Receiving</option>
					    	<optgroup id="options" label="Locations"></optgroup>
					    </select>
					    <img src="{{asset('images/preloader.gif')}}" id="from_loader" style="display: none;">
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
	$('#submitForm').click( function (e){
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
	})
	$('#barcode').change(function(){
		$('#options').html('');
		$('#barcode_loader').show();
		$('#from_loader').show();
		$('#expiration_date_select').html('');
		$('#expiration_date').removeAttr('disabled');
		$('#expiration_date').val('');
		$('#quantity').attr('max', '');
		$('#expiration_date').val('');
		$('#expiration_date').show();
		$('#expiration_date_select').hide();
		if ($(this).val() != '') {
			let barcode = $(this).val() ;
			$.ajax({
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
			       else{
			       	$('#from').select2();
			       	$('#barcode_loader').hide();
					$('#from_loader').hide();
					alert(response.error);
			       }

			     },
			     error: function (){

					$('#barcode_loader').hide();
					$('#from_loader').hide();
			     	alert("Something Went Wrong...");
			     }
			});
		}
	})
	$('#from').change(function(){

		$('#expiration_date_select').html('');

		$('#expiration_date').removeAttr('disabled');
		$('#expiration_date').val('');
		$('#expiration_date').show();
		$('#expiration_date_select').hide(); 
		$('#quantity').attr('max', '');
		if ($(this).val() != '' && $(this).val() != 'Receiving' && $('#barcode').val() != '') {
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
			       	   	html+= '<option value="'+response.data[1][`expiration`]+'" data-count="'+response.data[1][`count`]+'">' + date + '</option>';
			       	 }
			       	 else{
			       	   for(var index = 0; index < response.data.length; index++) {
			       	  	let date = (response.data[index][`expiration`] == null) ? 'None' : response.data[index][`expiration`];
			       	   	html+= '<option value="'+response.data[index][`expiration`]+'" data-count="'+response.data[index][`count`]+'">' + date + '</option>';
				       }
			       	 }


					    $('#expiration_date').hide();
				        $('#expiration_date_select').html(html); 
				        $('#expiration_date_select').show(); 
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
	function setExiprationDateAndQuantity(elem) {
		$('#quantity').attr('max', $(elem).find('option:selected').attr('data-count'));
	}
$(document).ready(function() {
    $('#from').select2();
});
</script>
@endsection