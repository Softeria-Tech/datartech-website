{{-- resources/views/livewire/register-with-verification.blade.php --}}
<div>
    <div class="max-w-2xl mx-auto">
        <!-- Phone Verification Section -->
        <div class="mb-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <livewire:pages.auth.phone-verification />
        </div>
        
        <!-- Registration Section (only show if phone is verified) -->
        @if(session('verified_phone'))
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <livewire:registration-form />
            </div>
        @endif
    </div>
</div>