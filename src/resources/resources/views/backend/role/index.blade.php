@extends('backend.layouts.main')

@section('content')

<!-- Modal -->

@php
	$modules = [];
	$modules['project'] = ['title' => 'project ko title'];
	$modules['client'] = ['title' => 'client ko title'];
	$modules['task'] = ['title' => 'task ko title'];
	$modules = array_map(function($modules){
	    return (object)$modules;
	}, $modules);
@endphp

<div class="row">
	<div class="col-md-5">
		<h3>Roles</h3>
	</div>
	<div class="col-md-7 page-action text-right">
		@can('add_roles')
		<a href="#" class="btn btn-sm btn-success pull-right" data-toggle="modal" data-target="#roleModal"> <i class="glyphicon glyphicon-plus"></i> create a new Role</a>
		@endcan
	</div>
</div>
<div class="row">
	<div class="col-md-3 mb-2 mt-1">
		<ul class="nav nav-pills flex-column mt-2" id="myTab" role="tablist">
			@foreach($modules as $module)
			<li class="nav-item">
				<a class="nav-link {{($loop->first) ? 'active' : '' }}" id="{{str_slug($module->title)}}-tab" data-toggle="tab" href="#{{str_slug($module->title)}}" role="tab" aria-controls="{{str_slug($module->title)}}" aria-selected="{{($loop->first) ? 'true' : 'false' }}">{{$module->title}}</a>
			</li>
			@endforeach
			
		</ul>
	</div>
	<!-- /.col-md-4 -->
	<div class="col-md-9">
		<div class="tab-content" id="myTabContent">
			@foreach($modules as $module)
			<div class="tab-pane fade  {{($loop->first) ? 'show active' : '' }}" id="{{str_slug($module->title)}}" role="tabpanel" aria-labelledby="{{str_slug($module->title)}}-tab">
				@foreach ($roles as $role)
					<form method="POST" action="{{ route('backend.roles.update',['id' => $role->id]) }}" accept-charset="UTF-8" class="m-b">
						@csrf
						@method('PUT')
						<div id="{{str_slug($role->name)}}-accordion">
							<div class="card">
								<div class="card-header" id="{{str_slug($role->name)}}-headingOne">
									<div class="d-flex justify-content-start w-100">
									
										<button type="button" class="btn btn-link text-left" data-toggle="collapse" data-target="#{{str_slug($role->name)}}-collapseOne" aria-expanded="{{($loop->first) ? 'true' : 'false' }}" aria-controls="{{str_slug($role->name)}}-collapseOne">
											{{ $role->name .' Permissions'}}
										</button>

										@if($role->name != 'Admin')
										<select class="form-control w-25" name="perm-select" onchange="perm_selector(this)">
											<option value="">Choose helpers</option>
											<option value="all-views-allow">Select All View Permission only</option>
											<option value="all-views-dismiss">De-Select All View Permission only</option>
											<option value="all-allow">Allow All Permissions</option>
											<option value="all-dismiss">De-Select All Permissions</option>
										</select>
										@endif
									</div>

								</div>

								<div id="{{str_slug($role->name)}}-collapseOne" class="collapse {{($loop->first) ? 'show' : '' }}" aria-labelledby="{{str_slug($role->name)}}-headingOne" data-parent="#{{str_slug($role->name)}}-accordion">
									<div class="card-body">
										<div class="row permissions">

											@foreach($permissions as $perm)
											<?php
											$per_found = null;
											if( isset($role) ) {
												$per_found = $role->hasPermissionTo($perm->name);
											}
											if( isset($user)) {
												$per_found = $user->hasDirectPermission($perm->name);
											}
											?>
											<div class="col-md-3">
												<div class="checkbox">
													<label class="{{ str_contains($perm->name, 'delete') ? 'text-danger' : '' }}" data-label="{{ str_contains($perm->name, 'view') ? 'view' : '' }}">
														@if ($per_found)
														<input checked="checked" name="permissions[]" type="checkbox" value="{{ $perm->name }}" @if($role->name == 'Admin') onclick='return false;' onkeydown='return false;' @endif > {{ $perm->name }}
														@else
														<input name="permissions[]" type="checkbox" value="{{ $perm->name }}">{{ $perm->name }}
														@endif
													</label>
												</div>
											</div>
											@endforeach

										</div>
										@can('edit_roles')
											@if($role->name != 'Admin')
												<input class="btn btn-primary" type="submit" value="Save">
											@endif
										@endcan
									</div>
								</div>
							</div>

						</div>



					</form>
				@endforeach
			</div>
			@endforeach


		</div>
	</div>
	<!-- /.col-md-8 -->
</div>
<!-- /.container -->



@endsection




@push('after-scripts')
<div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel">
	<div class="modal-dialog" role="document">
		<form method="POST" action="{{ route('backend.roles.index') }}" accept-charset="UTF-8" class="m-b">
			@csrf

			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="roleModalLabel">Role</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<!-- name Form Input -->
					<div class="form-group @if ($errors->has('name')) has-error @endif">
						<div class="form-group row mb-4">
							<label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" for="name">Role Name</label>
							<div class="col-sm-12 col-md-7">
								<input class="form-control" type="text" name="name" id="name" placeholder="Role Name" >
							</div>
						</div>
						@if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<!-- Submit Form Button -->
					<input class="btn btn-primary" type="submit" value="Save">
				</div>
			</div>
		</form>
	</div>
</div>


<script>
	function perm_selector(e){
		// console.log($(e).val());
		let perm = $(e).val();
		let all_perms = $(e).closest('.card-header').next().find('label');
		if(perm !== null){
			switch( perm ) {
				case 'all-views-allow':
					var views = all_perms.filter(function () {
                        return $(this).data("label") == "view";
                    });
                    views.each(function(){
		                $(this).find('input').prop('checked', true);
		            })
					
				break;
				case 'all-views-dismiss':
					var views = all_perms.filter(function () {
                        return $(this).data("label") == "view";
                    });
                    views.each(function(){
		                $(this).find('input').prop('checked', false);
		            })
					
				break;
				case 'all-allow':
					var views = all_perms.each(function(){
		                $(this).find('input').prop('checked', true);
		            })
					
				break;
				case 'all-dismiss': 
					var views = all_perms.each(function(){
		                $(this).find('input').prop('checked', false);
		            })
					
				break;
				
				default:
					console.log('whooooppssssss! That esculated quickly! Lets Reload Page and try!');
			} 
		}
	}

</script>

@endpush


