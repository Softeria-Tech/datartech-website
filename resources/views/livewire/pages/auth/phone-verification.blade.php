<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;

new #[Layout('frontend.layouts.library-app')] class extends Component
{
    public string $phone = '';
    public string $otp = '';
    public bool $otpSent = false;
    public bool $isVerified = false;
    public string $verificationId = '';
    public string $errorMessage = '';
    public int $countdown = 0;
    
    /**
     * Mount method - initialize component state
     */
    public function mount(): void
    {
        $user = auth()->user();
        if ($user && $user->phone) {
            $this->phone = $user->phone;
            if ($user->phone_verified_at) {
                $this->isVerified = true;
            }
        }
    }
    
    /**
     * Send OTP to the provided phone number
     */
    public function sendOtp(): void
    {
        $this->phone = optimizePhone($this->phone);

        $this->validate([
            'phone' => ['required', 'string', 'max:14','min:9','unique:users,phone,' . Auth::id()]
        ],[
            'phone.required' => 'Phone number is required',
            'phone.unique' => 'This phone number is already registered to another account.',
            'phone.max' => 'Invalid phone number',
            'phone.min' => 'Invalid phone number'
        ]);
        
        $this->errorMessage = '';
        
        try {
            $response = Http::post(config('services.sms_pro.url') . '/mverify', [
                'pro_api_key' => config('services.sms_pro.api_key'),
                'sender_name' => config('services.sms_pro.sender_name'),
                'mobiles' => $this->phone,
                'template' => 'Your verification code is: [otp]'
            ]);
            Log::info($response->body());
            if ($response->successful() && $response->json('status') === true) {
                $this->otpSent = true;
                $this->verificationId = $response->json('verification_id', uniqid());
                $this->startCountdown();
                session()->flash('message', 'OTP sent successfully!');
            } else {
                $this->errorMessage = $response->json('message') ?? 'Failed to send OTP. Please try again.';
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            $this->errorMessage = 'Unable to send OTP. Please check your connection.';
        }
    }
    
    /**
     * Verify the OTP code
     */
    public function verifyOtp(): void
    {
        $this->validate([
            'otp' => ['required', 'string'],
        ], [
            'otp.required' => 'OTP code is required'
        ]);
        
        $this->errorMessage = '';
        
        try {
            $response = Http::post(config('services.sms_pro.url') . '/mverify', [
                'pro_api_key' => config('services.sms_pro.api_key'),
                'mobiles' => $this->phone,
                'code' => $this->otp
            ]);
            
            if ($response->successful() && $response->json('status') === true) {
                $this->isVerified = true;
                
                // Store verified phone in session
                session(['verified_phone' => $this->phone]);
                $user = auth()->user();
                $user->phone = $this->phone;
                $user->phone_verified_at = Carbon::now();
                $user->update();
                
                // Emit event to notify registration form
                $this->dispatch('phone-verified', phone: $this->phone);
                
                session()->flash('success', 'Phone verified successfully!');
            } else {
                $this->errorMessage = $response->json('message') ?? 'Invalid OTP code. Please try again.';
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Unable to verify OTP. Please try again.';
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
        }
    }
    
    /**
     * Resend OTP
     */
    public function resendOtp(): void
    {
        if ($this->countdown > 0) {
            return;
        }
        
        $this->sendOtp();
    }
    
    /**
     * Start countdown timer for resend
     */
    protected function startCountdown(): void
    {
        $this->countdown = 60;
        
        $this->dispatch('start-countdown');
    }
    
    /**
     * Decrement countdown (called from JavaScript)
     */
    public function decrementCountdown(): void
    {
        if ($this->countdown > 0) {
            $this->countdown--;
        }
    }
    
    /**
     * Reset the verification process
     */
    public function openDashboard(): void
    {
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
<div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg" style="margin-top: 100px; margin-bottom: 100px;">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Verify Your Phone
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Enter your phone number to receive verification code
        </p>
    </div>
    
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    
    @if ($errorMessage)
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ $errorMessage }}
        </div>
    @endif
    
    @if (!$isVerified)
        <!-- Phone Input -->
        <div class="mb-4">
            <x-input-label for="phone" :value="__('Phone Number')" />
            <div class="flex gap-2">
                <x-text-input wire:model="phone"  id="phone"  class="block mt-1 w-full" type="tel"  name="phone" 
                    required  autocomplete="phone" :disabled="$otpSent" placeholder="e.g., 0712509826"
                />
                @if (!$otpSent)
                    <x-primary-button wire:click="sendOtp" class="mt-1 whitespace-nowrap" :disabled="$otpSent">
                        Send OTP
                    </x-primary-button>
                @endif
            </div>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>
        
        @if ($otpSent)
            <!-- OTP Input -->
            <div class="mb-4">
                <x-input-label for="otp" :value="__('Verification Code')" />
                <div class="flex gap-2">
                    <x-text-input 
                        wire:model="otp" id="otp" class="block mt-1 w-full"  type="text" 
                        name="otp" required placeholder="Enter your code" maxlength="4"
                    />
                    <x-primary-button wire:click="verifyOtp" class="mt-1 whitespace-nowrap" >
                        Verify
                    </x-primary-button>
                </div>
                <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    @if ($countdown > 0)
                        Resend available in {{ $countdown }} seconds
                    @else
                        <button wire:click="resendOtp" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400" >
                            Resend OTP
                        </button>
                    @endif
                </div>
            </div>
        @endif
    @else
        <!-- Verified Success Message -->
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-center">
            <svg class="w-12 h-12 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-lg font-semibold">Phone Verified!</h3>
            <p class="text-sm">Your phone number {{ $phone }} has been verified.</p>
            <button wire:click="openDashboard" class="mt-2 text-sm text-indigo-600 hover:text-indigo-900" >
                Go To Dashboard
            </button>
        </div>
    @endif

    @include('frontend.layouts.partials.loading-indicator')

</div>
</div>

@push('scripts')
<script>
    /*document.addEventListener('livewire:initialized', () => {
        let countdownInterval = null;
        
        Livewire.on('start-countdown', () => {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            countdownInterval = setInterval(() => {
                //@this.call('decrementCountdown');
            }, 1000);
        });
    });*/
</script>
@endpush