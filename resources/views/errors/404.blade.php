@extends('errors.layout')

@section('title', 'Not Found')

@section('content')
    <div class="icon-container">
        <i class="fas fa-search"></i>
    </div>
    
    <div class="error-code">404</div>
    
    <h1 class="error-title">Page Not Found</h1>
    
    <p class="error-message">
        We couldn't find the page you are looking for. It might have been removed, renamed, or did not exist in the first place.
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
