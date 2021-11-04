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
				<h2>Add Inventory Location</h2>
				 @if (Session::get('danger')) 
			    	@foreach (Session::get('danger')[0] as $error)
			    		<div class="alert alert-danger">{{$error}}</div>
			    	@endforeach
			    @endif
			</div>
			<div class="alert"></div>
			<div class="wc-content">
				<form class="custom_form" action="{{route('addusers')}}" method="POST" enctype="multipart/form-data" id="saveInventory">
					   @csrf
					   <div id="fileFields"></div>
					  <div class="form-group col-half">
					    <label for="barcode">Barcode</label>
					    <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Enter Barcode">
					  </div>
					  <div class="form-group col-half">
					    <label for="quantity">Quantity</label>
					    <input type="number" class="form-control" min="1" id="quantity" placeholder="Enter Quantity" name="quantity">
					  </div>
					  <div class="form-group col-half">
					    <label for="from">From Location</label>		
					    <select class="form-control" id="from" placeholder="From Location" name="from">
					    	<option value="">Select From</option>
					    	<option value="Receiving">Receiving</option>
					    	<optgroup id="options" label="Locations"></optgroup>
					    </select>
					  </div>
					  <div class="form-group col-half">
					    <label for="to">To Location</label>
					    <input type="text" class="form-control" id="to" placeholder="To Location" name="to">
					  </div>
					  <div class="form-group col-half">
					    <label for="expiration_date">Expiration Date</label>
					    <input type="text" class="form-control" id="expiration_date" placeholder="Expiration Date" name="expiration_date">
					    <div id="available_expiration_date"></div>
					  </div>
					  <div class="form-group col-half">
					    <label for="pallet_number">Pallet number</label>
					    <input type="text" class="form-control" id="pallet_number" placeholder="Pallet Number" name="pallet_number">
					  </div>
					  <div class="form-group col-full">
					    
					    <button type="button" id="imageUploader">Upload Images <img src="{{asset('images/upload_img.png')}}"></button>
					    <input type="file" id="fileupload" class="form-control" style="visibility: hidden; opacity: 0;" id="images" name="upimages[]" multiple="">
					    <div id="preview"></div>
					  </div>

					  <div class="btn-form">
					  	<button type="submit" class="btn btn-primary">Submit</button>
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
		$('.btn-form img').css("display","inline-block");
		e.preventDefault();
		var formData = new FormData(this);
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
		$('#available_expiration_date').html('');
		$('#expiration_date').removeAttr('disabled');
		$('#expiration_date').val('');
		$('#quantity').attr('max', '');
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
			       if (response.status == 'success') {
			       		let html = '';
			       	   for(var index = 0; index < response.locations.length; index++) {
			       	   	html+= '<option value="'+response.locations[index]+'">' + response.locations[index] + '</option>';
				       }
				       $('#options').html(html); 
			       }
			       else if (response.status == 'error'){
				       	alert(response.error);
			       }

			     },
			     error: function (){
			     	alert("Something Went Wrong...");
			     }
			});
		}
	})
	$('#from').change(function(){

		$('#available_expiration_date').html('');
		$('#expiration_date').removeAttr('disabled');
		$('#expiration_date').val('');
		$('#quantity').attr('max', '');
		if ($(this).val() != '' && $(this).val() != 'Receiving' && $('#barcode').val() != '') {
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
			       if (response.status == 'success') {
			       	let html = '<table id="available_expiration" class="table table-bordered table-striped"><thead><tr><th colspan="5" class="text-center">Available Expiration Date.</th></tr><tr><th>Barcode</th><th>Location</th><th>Quantity</th><th>Expiration Date</th><th>Action</th></tr></thead><tbody>';
			       	  for(var index = 0; index < response.data.length; index++) {
			       	   	html+= `<tr><td>`+$('#barcode').val()+`</td><td>`+from+`</td><td>`+response.data[index][`count`]+`</td><td>`+response.data[index][`expiration`]+`</td><td><a href="javascript:void(0)" class="btn btn-primary" onclick="setExiprationDateAndQuantity('`+response.data[index][`expiration`]+`', '`+response.data[index][`count`]+`')">Select this</a></td></tr>`;
				       }
				       html+= '</tbody></table>';
				        $('#available_expiration_date').html(html); 
						$('#expiration_date').attr('disabled', 'disabled');
						$('#available_expiration_date table').DataTable({  "info":     false});
			       }
			       else if (response.status == 'error'){
				       	alert(response.error);
			       }

			     },
			     error: function (){
			     	alert("Something Went Wrong...");
			     }
			});
		}
	})
	function setExiprationDateAndQuantity(date, quantity) {
		if (date == '') {
			$('#expiration_date').removeAttr('disabled');

			$('#expiration_date').val('');
			alert('Note: Expiration date is empty in this record.');
		}
		else{
			$('#expiration_date').val(date);
			$('#quantity').attr('max', quantity);
		}
	}

</script>
@endsection