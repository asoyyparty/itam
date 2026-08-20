@extends('errors.layout')

@section('title', 'Server Error')

@section('content')
    <div class="icon-container" style="color: #ef4444; filter: drop-shadow(0 0 20px rgba(239, 68, 68, 0.4));">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    
    <div class="error-code">500</div>
    
    <h1 class="error-title">Internal Server Error</h1>
    
    <p class="error-message">
        Oops, something went wrong on our end. We are currently trying to fix the problem. <br>
        Please try again later or contact support if the issue persists.
    </p>
    
    <div class="action-buttons">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-primary" style="background-color: #ef4444; border-color: #ef4444; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </div>
@endsection
