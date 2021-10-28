@extends('layouts.app')

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
				<form action="{{route('addusers')}}" method="POST" enctype="multipart/form-data" id="saveInventory">
					   @csrf
					   <div id="fileFields"></div>
					  <div class="form-group">
					    <label for="barcode">Barcode</label>
					    <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Enter Barcode">
					  </div>
					  <div class="form-group">
					    <label for="quantity">Quantity</label>
					    <input type="number" class="form-control" id="quantity" placeholder="Enter Quantity" name="quantity">
					  </div>
					  <div class="form-group">
					    <label for="from">From Location</label>
					    <input type="text" class="form-control" id="from" placeholder="From Location" name="from">
					  </div>
					  <div class="form-group">
					    <label for="to">To Location</label>
					    <input type="text" class="form-control" id="to" placeholder="From Location" name="to">
					  </div>
					  <div class="form-group">
					    <label for="expiration_date">Expiration Date</label>
					    <input type="date" class="form-control" id="expiration_date" placeholder="Expiration Date" name="expiration_date">
					  </div>
					  <div class="form-group">
					    <label for="pallet_number">Pallet number</label>
					    <input type="text" class="form-control" id="pallet_number" placeholder="Pallet Number" name="pallet_number">
					  </div>
					  <div class="form-group">
					    <label for="images">Images</label>
					    <button type="button" id="imageUploader"><i class="fas fa-plus"></i></button>
					    <input type="file" id="fileupload" class="form-control" style="visibility: hidden; opacity: 0;" id="images" name="upimages[]" multiple="">
					    <div id="preview"></div>
					  </div>

					  <button type="submit" class="btn btn-primary">Submit</button>
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
		       if (response.status == 'success') {
   			       for(var index = 0; index < response.files.length; index++) {
			         var src = "{{ asset('uploads/') }}" + "/" + response.files[index];

			         $('#preview').append('<div><i class="far fa-times-circle" onclick="removeImages(this)" data-id="uImg'+index+'" imgsrc="'+response.files[index]+'"></i><img src="'+src+'" width="200px;" height="200px"></div>');
			         

			         $('#fileFields').append('<input id="uImg'+index+'" type="hidden" name="images[]" value="'+response.files[index]+'" >');
			       }
		       }
		       else{
			       	alert(response.error);
		       }

		     },
		     error: function (){

		     }
		});
	})

	function removeImages(elem){
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
		        if (response.status == 'success') {
		       		$(elem).parent('div').remove();
		       		$('#' +$(elem).attr('data-id')).remove();
		        }
		        else{
			       	alert(response.error);
		        }
		     }
		});
	}

	$('#saveInventory').submit( function (e){
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

		     }
		});
	})


</script>
@endsection