@extends('layouts.app')

@section('content')

	<div class="col-lg-9 col-md-9">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>Add Role</h2>
			</div>
			 @if (Session::get('danger')) 
		    	@foreach (Session::get('danger')[0] as $error)
		    		<div class="alert alert-danger">{{$error}}</div>
		    	@endforeach
		    @endif
			<div class="wc-content">
				<form action="{{route('store_role')}}" method="POST" autocomplete="off">
					   @csrf
					  <div class="form-group">
					    <label for="username">Name</label>
					    <input type="text" class="form-control" id="username" name="name" placeholder="Enter Name">		   
					  </div>
                      <p>User</p>
                      <hr>
                      <div class="form-group">
                        <label for="user_view_all">
                            <input id="user_view_all" type="checkbox" name="permission[view_all_users]" >
                            View ALL User
                        </label>

                        <label class="ml-4" for="user_add">
                            <input id="user_add" type="checkbox" name="permission[user_add]" >
                            Add New User
                        </label>
                        <label class="ml-4" for="user_update">
                            <input id="user_update" type="checkbox" name="permission[user_update]" >
                            Update  User
                        </label>
                        <label class="ml-4" for="user_delete">
                            <input id="user_delete" type="checkbox" name="permission[user_delete]" >
                            Delete New User
                        </label>
                      </div>
                      <p>Role</p>
                      <hr>
                      <div class="form-group">
                        <label for="view_all_role">
                            <input id="view_all_role" type="checkbox" name="permission[view_all_role]" >
                            View ALL Roles
                        </label>

                        <label class="ml-4" for="role_add">
                            <input id="role_add" type="checkbox" name="permission[role_add]" >
                            Add New Role
                        </label>
                        <label class="ml-4" for="role_update">
                            <input id="role_update" type="checkbox" name="permission[role_update]" >
                            Update Role
                        </label>
                        <label class="ml-4" for="role_delete">
                            <input id="role_delete" type="checkbox" name="permission[role_delete]" >
                            Delete Role
                        </label>
                      </div>
                      <p>Items</p>
                      <hr>
                      <div class="form-group">
                        <label for="view_all_item">
                            <input id="view_all_item" type="checkbox" name="permission[view_all_item]" >
                             ALL Items
                        </label>

                        <label class="ml-4" for="item_skip">
                            <input id="item_skip" type="checkbox" name="permission[item_skip]" >
                            Item Skipped
                        </label>
                        
                      </div>
                      <p>Inventory</p>
                      <hr>
                      <div class="form-group">
                        <label for="inventory_view_on_hand">
                            <input id="inventory_view_on_hand" type="checkbox" name="permission[inventory_view_on_hand]" >
                            All on Hands
                        </label>

                        <label class="ml-4" for="inventory_view_on_receive">
                            <input id="inventory_view_on_receive" type="checkbox" name="permission[inventory_view_on_receive]" >
                            All on Receive
                        </label>
                        <label class="ml-4" for="inventory_import">
                            <input id="inventory_import" type="checkbox" name="permission[inventory_import]" >
                            Import
                        </label>
                      </div>
                      <p>Inventory Location Tracking</p>
                      <hr>
                      <div class="form-group">
                        <label for="scan_inventroy">
                            <input id="scan_inventroy" type="checkbox" name="permission[scan_inventroy]" >
                            Scan Inventory 
                        </label>

                        <label class="ml-4" for="inventory_location">
                            <input id="inventory_location" type="checkbox" name="permission[inventory_location]" >
                            Location
                        </label>
                        <label class="ml-4" for="inventory_adjustment">
                            <input id="inventory_adjustment" type="checkbox" name="permission[inventory_adjustment]"  >
                            Adjustment
                        </label>
                      
                      </div>
					  <button type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
		</div>
	</div>

@endsection