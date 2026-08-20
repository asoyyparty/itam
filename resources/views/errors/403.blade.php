@extends('errors.layout')

@section('title', 'Forbidden')

@section('content')
    <div class="icon-container">
        <i class="fas fa-lock"></i>
    </div>
    
    <div class="error-code">403</div>
    
    <h1 class="error-title">Access Denied</h1>
    
    <p class="error-message">
        Sorry, you don't have permission to access this page or perform this action. <br>
        If you believe this is a mistake, please contact your administrator.
    </p>
    
    <div class="action-buttons">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </div>
@endsection
