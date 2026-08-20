@extends('errors.layout')

@section('title', 'Service Unavailable')

@section('content')
    <div class="icon-container" style="color: #f59e0b; filter: drop-shadow(0 0 20px rgba(245, 158, 11, 0.4));">
        <i class="fas fa-tools"></i>
    </div>
    
    <div class="error-code">503</div>
    
    <h1 class="error-title">Under Maintenance</h1>
    
    <p class="error-message">
        We are currently performing scheduled maintenance on the system. <br>
        We should be back online shortly. Thank you for your patience!
    </p>
    
    <div class="action-buttons">
        <a href="javascript:location.reload();" class="btn btn-primary" style="background-color: #f59e0b; border-color: #f59e0b; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);">
            <i class="fas fa-sync-alt"></i> Refresh Page
        </a>
    </div>
@endsection
