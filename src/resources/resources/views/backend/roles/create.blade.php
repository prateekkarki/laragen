@extends('backend.layouts.main')

@push('page-styles')
    <link rel="stylesheet" href="{{ asset('css/summernote-bs4.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/bootstrap-timepicker.min.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}" >
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
            <i class="fa fa-save"></i> Create role
        </button>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Create roles</h4>
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

        <form action="{{ route('backend.roles.store') }}" enctype="multipart/form-data" method="POST" id="role-form">
            @csrf
            <div class="row mt-4 mb-4">
                <div class="col">
                    
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" for="name">Name</label>
                        <div class="col-sm-12 col-md-7">
                            <input class="form-control @if ($errors->has('name')) is-invalid @endif" type="text" name="name" id="name" placeholder="Name" value="{{ old('name') }}" >
                            @if ($errors->has('name'))
                                <span class="invalid-feedback">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3" for="permissions[]">Permissions</label>
                        <div class="col-sm-12 col-md-7">
                            <select multiple class="form-control select2 @if ($errors->has('permissions')) is-invalid @endif" id="permissions" name="permissions[]">
                                @foreach($permissions as $p_permission)
                                    <option value="{{ $p_permission->id }}" {{ 
                                        (collect(old('permissions'))->contains($p_permission->id)) ? 'selected' : ''
                                    }}>{{ $p_permission->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('permissions'))
                                <span class="invalid-feedback">{{ $errors->first('permissions') }}</span>
                            @endif
                        </div>
                    </div>
                
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-save"></i> Create role
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
@endpush
