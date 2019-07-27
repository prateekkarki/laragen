@extends('backend.layouts.main')

@push('page-styles')
    <link rel="stylesheet" href="{{ asset('css/summernote-bs4.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/bootstrap-timepicker.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/chocolat.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/iziToast.min.css') }}" >

    <style>
        #accordion a {
          display: block;
          padding: 10px 15px;
          border-bottom: 1px solid #6777ef;
          text-decoration: none;
          text-align: left;
        }
        #accordion .panel-heading a.collapsed:hover,
        #accordion .panel-heading a.collapsed:focus {
          background-color: #6777ef;
          color: white;
          transition: all 0.2s ease-in;
        }
        #accordion .panel-heading a.collapsed:hover::before,
        #accordion .panel-heading a.collapsed:focus::before {
          color: white;
        }
        #accordion .panel-heading {
          padding: 0;
          border-radius: 0px;
          text-align: center;
        }
        #accordion .panel-heading a:not(.collapsed) {
          color: white;
          background-color: #6777ef;
          transition: all 0.2s ease-in;
        }

        /* Add Indicator fontawesome icon to the left */
        #accordion .panel-heading .accordion-toggle .fas:before {
          float: left;
          color: white;
          font-weight: lighter;
          transform: rotate(-135deg);
          transition: all 0.2s ease-in;
        }
        #accordion .panel-heading .accordion-toggle.collapsed .fas:before {
          color: #444;
          transform: rotate(0deg);
          transition: all 0.2s ease-in;
        }


    </style>
@endpush

@section('page-header')
    <div class="title-box col-md-7">
        <div class="section-header-back">
            <a href="{{ route('backend.roles.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>
            Role Management
        </h1>
    </div>
    <div class="button-box col-md-5">
		<button class="btn btn-primary" type="button" onclick="document.getElementById('role-form').submit();">
			<i class="fa fa-save"></i> Update role
		</button>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Update Role</h4>
    </div>
    <div class="card-body">
	    @if ($errors->any())
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
		@endif    
        
        <form action="{{ route('backend.roles.update', $role) }}" enctype="multipart/form-data" method="POST" id="role-form">
            @csrf
            @method('PUT')
            <div class="row mt-4 mb-4">
                <div class="col">
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" for="name">Name</label>
                            <div class="col-sm-12 col-md-7">
                                <input class="form-control @if ($errors->has('name')) is-invalid @endif" type="text" name="name" id="name" value="{{ old('name') ?? $role->name }}" placeholder="Name" >
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" for="permissions[]">Permissions</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="container">
                                    <div id="accordion" class="panel-group">
                                        @foreach ($groupedPermissions as $module => $permissions)
                                            <div class="panel">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a href="#panelBody{{ str_replace(" ", "", $module) }}" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">
                                                           <i class="fas fa-plus"></i> {{ $module }}
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="panelBody{{ str_replace(" ", "", $module) }}" class="panel-collapse collapse ">
                                                    <div class="panel-body">
                                                        @foreach ($permissions as $key => $p_permission)
                                                            <label class="custom-switch" for="pc{{ str_replace(" ", "", $module) }}{{$key}}">
                                                                <input type="checkbox" class="form-control custom-switch-input" id="pc{{ str_replace(" ", "", $module) }}{{$key}}" {{
                                                                        old('permissions') ? ((collect(old('permissions'))->contains($p_permission->id)) ? 'checked' : '') : (in_array($p_permission->id, $role->permissions->pluck('id')->toArray())) ? 'checked' : ''
                                                                }}>
                                                                <input type="hidden"  {{
                                                                        old('permissions') ? ((collect(old('permissions'))->contains($p_permission->id)) ? "name=permissions[]" : '') : (in_array($p_permission->id, $role->permissions->pluck('id')->toArray())) ? "name=permissions[]" : ''
                                                                }} class="hidden-permission" value="{{ $p_permission->id }}">
                                                                <span class="custom-switch-indicator"></span>
                                                            </label>
                                                            <label class="col-sm-10" for="pc{{ str_replace(" ", "", $module) }}{{$key}}">{{ ucfirst($p_permission->name) }} </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-save"></i> Update role
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('page-scripts')
    <script src="{{ asset('js/summernote-bs4.js') }}"></script>
    <script src="{{ asset('js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <script src="{{ asset('js/jquery.chocolat.min.js') }}"></script>
    <script src="{{ asset('js/iziToast.min.js') }}"></script>

    <script>
        $('.custom-switch-input').on('click', function(){
            if (this.checked) 
                $(this).parent().find('.hidden-permission').attr('name', 'permissions[]'); 
            else { 
                $(this).parent().find('.hidden-permission').removeAttr("name");
            }
        });
    </script>
@endpush
