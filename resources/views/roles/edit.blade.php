@extends('layouts.app')

@section('content')

	<div class="col-lg-9 col-md-9">
		<div class="wrap-content">
			<div class="wc-title">
				<h2>Add Role</h2>
			</div>
            @if($errors->any())
            <div class="alert alert-danger">
                <p><strong>Opps Something went wrong</strong></p>
                <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </div>
             @endif
			<div class="wc-content">
				<form action="{{route('update_role',$role->id)}}" method="POST" autocomplete="off">
					   @csrf
					  <div class="form-group">
					    <label for="username">Name</label>
					    <input type="text" class="form-control" id="username" name="name" value="{!! $role->name !!}" placeholder="Enter Name">		   
					  </div>
                      <p>User</p>
                      <hr>
                      <div class="form-group">
                        <label for="user_view_all">
                            <input id="user_view_all" type="checkbox" name="permission[view_all_users]" {!! in_array('view_all_users',$abilitiesarray) == true ?"checked":"" !!}>
                            View ALL User
                        </label>

                        <label class="ml-4" for="user_add">
                            <input id="user_add" type="checkbox" name="permission[user_add]" {!! in_array('user_add',$abilitiesarray) == true ?"checked":"" !!} >
                            Add New User
                        </label>
                        <label class="ml-4" for="user_update">
                            <input id="user_update" type="checkbox" name="permission[user_update]" {!! in_array('user_update',$abilitiesarray) == true ?"checked":"" !!} >
                            Update  User
                        </label>
                        <label class="ml-4" for="user_delete">
                            <input id="user_delete" type="checkbox" name="permission[user_delete]" {!! in_array('user_delete',$abilitiesarray) == true ?"checked":"" !!} >
                            Delete New User
                        </label>
                      </div>
                      <p>Role</p>
                      <hr>
                      <div class="form-group">
                        <label for="view_all_role">
                            <input id="view_all_role" type="checkbox" name="permission[view_all_role]" {!! in_array('view_all_role',$abilitiesarray) == true ?"checked":"" !!} >
                            View ALL Roles
                        </label>

                        <label class="ml-4" for="role_add">
                            <input id="role_add" type="checkbox" name="permission[role_add]" {!! in_array('role_add',$abilitiesarray) == true ?"checked":"" !!} >
                            Add New Role
                        </label>
                        <label class="ml-4" for="role_update">
                            <input id="role_update" type="checkbox" name="permission[role_update]" {!! in_array('role_update',$abilitiesarray) == true ?"checked":"" !!} >
                            Update Role
                        </label>
                        <label class="ml-4" for="role_delete">
                            <input id="role_delete" type="checkbox" name="permission[role_delete]" {!! in_array('role_delete',$abilitiesarray) == true ?"checked":"" !!} >
                            Delete Role
                        </label>
                      </div>
                      <p>Items</p>
                      <hr>
                      <div class="form-group">
                        <label for="view_all_item">
                            <input id="view_all_item" type="checkbox" name="permission[view_all_item]" {!! in_array('view_all_item',$abilitiesarray) == true ?"checked":"" !!} >
                             ALL Items
                        </label>

                        <label class="ml-4" for="item_skip">
                            <input id="item_skip" type="checkbox" name="permission[item_skip]" {!! in_array('item_skip',$abilitiesarray) == true ?"checked":"" !!} >
                            Item Skipped
                        </label>
                        <label class="ml-4" for="importnolocation">
                            <input id="importnolocation" type="checkbox" name="permission[importnolocation]" {!! in_array('importnolocation',$abilitiesarray) == true ?"checked":"" !!}>
                            Add Ridgetfield Inventory to Nolocation 
                        </label>
                        <label class="ml-4" for="removenolocation">
                            <input id="removenolocation" type="checkbox" name="permission[removenolocation]" {!! in_array('removenolocation',$abilitiesarray) == true ?"checked":"" !!}>
                            Remove Ridgetfield Inventory From Nolocation
                        </label>
                        
                      </div>
                      <p>Replen Batches </p>
                      <hr>
                      <div class="form-group">
                        <label for="replen_batches">
                            <input id="replen_batches" type="checkbox" name="permission[replen_batches]"  {!! in_array('replen_batches',$abilitiesarray) == true ?"checked":"" !!}>
                            Replen Batches List
                        </label>

                        <label class="ml-4" for="replen_batches_details">
                            <input id="replen_batches_details" type="checkbox" name="permission[replen_batches_details]"  {!! in_array('replen_batches_details',$abilitiesarray) == true ?"checked":"" !!}>
                            Replen View Details
                        </label>
                        <label class="ml-4" for="replen_batches_export">
                            <input id="replen_batches_export" type="checkbox" name="permission[replen_batches_export]"  {!! in_array('replen_batches_export',$abilitiesarray) == true ?"checked":"" !!}>
                            Replen Export
                        </label>
                      </div>
                      <p>Inventory</p>
                      <hr>
                      <div class="form-group">
                        <label for="inventory_view_on_hand">
                            <input id="inventory_view_on_hand" type="checkbox" name="permission[inventory_view_on_hand]" {!! in_array('inventory_view_on_hand',$abilitiesarray) == true ?"checked":"" !!} >
                            All on Hands
                        </label>

                        <label class="ml-4" for="inventory_view_on_receive">
                            <input id="inventory_view_on_receive" type="checkbox" name="permission[inventory_view_on_receive]" {!! in_array('inventory_view_on_receive',$abilitiesarray) == true ?"checked":"" !!} >
                            All on Receive
                        </label>
                        <label class="ml-4" for="inventory_import">
                            <input id="inventory_import" type="checkbox" name="permission[inventory_import]" {!! in_array('inventory_import',$abilitiesarray) == true ?"checked":"" !!} >
                            Import
                        </label>
                      </div>
                      <p>Inventory Location Tracking</p>
                      <hr>
                      <div class="form-group">
                        <label for="scan_inventroy">
                            <input id="scan_inventroy" type="checkbox" name="permission[scan_inventroy]" {!! in_array('scan_inventroy',$abilitiesarray) == true ?"checked":"" !!} >
                            Scan Inventory 
                        </label>
                        <label for="scan_inventroy_u">
                            <input id="scan_inventroy_u" type="checkbox" name="permission[scan_inventroy_u]" {!! in_array('scan_inventroy_u',$abilitiesarray) == true ?"checked":"" !!}>
                            Scan Inventory Unique
                        </label>

                        <label class="ml-4" for="inventory_location">
                            <input id="inventory_location" type="checkbox" name="permission[inventory_location]" {!! in_array('inventory_location',$abilitiesarray) == true ?"checked":"" !!} >
                            Location
                        </label>

                        <label for="inventory_adjustment">
                            <input id="inventory_adjustment" type="checkbox" name="permission[inventory_adjustment]" {!! in_array('inventory_adjustment',$abilitiesarray) == true ?"checked":"" !!} >
                            Adjustment
                        </label>
                      
                      </div>
					  <button type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
		</div>
	</div>

@endsection