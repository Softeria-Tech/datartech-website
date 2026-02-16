@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')

@section('message')
    <div>
        <p class="mb-4">{{ __($exception->getMessage() ?: 'Forbidden') }}</p>
        
        @auth
            <a href="{{ route('dashboard') }}" 
               style="padding: 10px 20px; background-color: #4f46e5; color: white; border-radius: 5px; text-decoration: none;">
                Go to Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" 
               style="padding: 10px 20px; background-color: #1f2937; color: white; border-radius: 5px; text-decoration: none;">
                Login to Continue
            </a>
        @endauth
    </div>
@endsection