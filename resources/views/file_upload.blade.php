@extends('layouts.app')
@section('headermeta')
<style type="text/css">
	input#file_upload.data_file {
    background: url('{{asset("images/upload_img.png")}}');
}
</style>
@endsection
@section('content')

	<div class="col-lg-9 col-md-9">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>Import File</h2>

			</div>
				@if (Session::get('danger')) 
			    	@foreach (Session::get('danger')[0] as $error)
			    		<div class="alert alert-danger">{{$error}}</div>
			    	@endforeach
			    @endif
				@if (session('success'))
				    <div class="alert alert-success mb-3">
				        {{ session('success') }}
				    </div>
				@endif
			<div class="wc-content">
				<form class="custom_form" action="{{route('saveImportFiles')}}" method="POST" enctype="multipart/form-data">
					   @csrf
					  <div class="form-group input-radio">
					    <label for="type">File Type</label><br>
					    <label class="label" for="on_hand"><input type="radio"  id="on_hand" name="type" value="on_hand">On Hand<span class="checkmark"></span></label>
					    <label class="label" for="on_reciving"><input type="radio"  id="on_reciving" name="type" value="on_reciving">	On Reciving	   <span class="checkmark"></span>
						</label>
					  </div>
					  <div class="form-group">
					    <label for="email">Upload File</label>
					    <input type="file" class="form-control data_file" id="file_upload" name="file_upload">
					    <span class="attach-file"></span>
					  </div>

					  <button type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
		</div>
	</div>

@endsection


@section('script')

<script type="text/javascript">
	
	$('#file_upload').on("change",function(){
	    if($(this).val() != ''){
	    }
	    else{
	    }
	});


	$('#file_upload').on("change",function(){
	    if($(this).val() != ''){

	        
	        let strpVal = $(this).val();
	        strpVal = strpVal.replace(/\\/g, '/').replace(/.*\//, '');
	        $('span.attach-file').text(strpVal);
	        $('span.attach-file').addClass("equipped");
	    }
	    else{
	       $('span.attach-file').text('Attach File'); 
	       $('span.attach-file').removeClass("equipped");
	    }
	});

</script>


@endsection