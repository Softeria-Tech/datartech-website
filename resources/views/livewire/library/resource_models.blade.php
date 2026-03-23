    {{-- Preview Modal --}}
    @if ($showPreviewModal && $previewResource)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                    wire:click="$set('showPreviewModal', false)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">

                    {{-- Modal Header --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $previewResource->title }}
                            </h3>
                            <button wire:click="$set('showPreviewModal', false)"
                                class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            @if ($previewResource->author)
                                By {{ $previewResource->author }} ·
                            @endif
                            {{ $previewResource->category?->name }}
                        </p>
                    </div>

                    {{-- Preview Content --}}
                    <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                        @if ($previewFileUrl)
                            @php
                                $extension = pathinfo(
                                    $previewResource->file_path ?? $previewResource->preview_file_path,
                                    PATHINFO_EXTENSION,
                                );
                            @endphp

                            @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $previewFileUrl }}" alt="{{ $previewResource->title }}"
                                    class="max-w-full h-auto mx-auto rounded-lg shadow-lg">
                            @elseif($extension === 'pdf')
                                <iframe src="{{ $previewFileUrl }}" style="width:100%; height:600px;" frameborder="0"
                                    class="rounded-lg">
                                </iframe>
                            @elseif(in_array($extension, ['doc', 'docx']))
                                <iframe
                                    src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($previewFileUrl) }}"
                                    style="width:100%; height:600px;" frameborder="0" class="rounded-lg">
                                </iframe>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="mt-4 text-gray-600 dark:text-gray-400">
                                        Preview not available for this file type.
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="prose dark:prose-invert max-w-none">
                                {!! $previewResource->description !!}
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div>
                                @if ($previewResource->discount_price && $previewResource->discount_ends_at?->isFuture())
                                    <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                        Ksh{{ number_format($previewResource->discount_price) }}
                                    </span>
                                    <span class="ml-2 text-sm text-gray-500 line-through">
                                        Ksh{{ number_format($previewResource->price) }}
                                    </span>
                                @else
                                    <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                        Ksh{{ number_format($previewResource->price) }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex gap-3">
                                <button wire:click="$set('showPreviewModal', false)"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white text-sm font-medium rounded-lg transition">
                                    Close
                                </button>

                                @auth
                                    @if (!$this->userHasPurchased($previewResource->id))
                                        <button wire:click="initiatePurchase('{{ $previewResource->slug }}')"
                                            wire:click="$set('showPreviewModal', false)"
                                            class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                                            Purchase Now
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login', ['redirect' => route('library.resources')]) }}"
                                        class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                                        Purchase
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick View Modal --}}
    @if ($showQuickView && $quickViewResource)
        <div class="fixed inset-0 z-40 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
                    wire:click="$set('showQuickView', false)"></div>

                <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full p-6">
                    <button wire:click="$set('showQuickView', false)"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="flex gap-6">
                        <div class="w-1/3">
                            @if ($quickViewResource->thumbnail)
                                <img src="{{ Storage::url($quickViewResource->thumbnail) }}"
                                    alt="{{ $quickViewResource->title }}" class="w-full rounded-lg shadow-lg">
                            @else
                                <div
                                    class="w-full aspect-square bg-gray-100 dark:bg-gray-900 rounded-lg flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="w-2/3">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ $quickViewResource->title }}
                            </h3>

                            @if ($quickViewResource->author)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    By {{ $quickViewResource->author }}
                                </p>
                            @endif

                            <div class="flex gap-2 mb-3">
                                @if ($quickViewResource->category)
                                    <span
                                        class="px-2 py-1 bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 text-xs rounded-full">
                                        {{ $quickViewResource->category->name }}
                                    </span>
                                @endif
                                <span
                                    class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs rounded-full">
                                    {{ strtoupper(pathinfo($quickViewResource->file_path ?? $quickViewResource->external_url, PATHINFO_EXTENSION) ?? 'PDF') }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {!! $quickViewResource->description !!}
                            </p>

                            <div class="space-y-2 mb-4">
                                @if ($quickViewResource->page_count)
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>{{ $quickViewResource->page_count }} pages</span>
                                    </div>
                                @endif
                                @if ($quickViewResource->language)
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                                d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                        </svg>
                                        <span>{{ ucfirst($quickViewResource->language) }}</span>
                                    </div>
                                @endif
                                @if ($quickViewResource->version)
                                    <div class="flex items-center text-sm">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        <span>Version {{ $quickViewResource->version }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    @if ($quickViewResource->discount_price && $quickViewResource->discount_ends_at?->isFuture())
                                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                            Ksh{{ number_format($quickViewResource->discount_price) }}
                                        </span>
                                        <span class="ml-2 text-sm text-gray-500 line-through">
                                            Ksh{{ number_format($quickViewResource->price) }}
                                        </span>
                                    @else
                                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                            Ksh{{ number_format($quickViewResource->price) }}
                                        </span>
                                    @endif
                                </div>

                                @auth
                                    @if (!$this->userHasPurchased($quickViewResource->id))
                                        <button wire:click="initiatePurchase('{{ $quickViewResource->slug }}')"
                                            wire:click="$set('showQuickView', false)"
                                            class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">
                                            Purchase Now
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login', ['redirect' => route('library.resources')]) }}"
                                        class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">
                                        Purchase
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Purchase Modal --}}
    @if ($showPurchaseModal && $purchaseResource)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                    wire:click="$set('showPurchaseModal', false)"></div>

                <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Complete Your Purchase
                        </h3>
                        <button wire:click="$set('showPurchaseModal', false)"
                            class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mb-6">
                        <div class="flex gap-4 mb-4">
                            @if ($purchaseResource->thumbnail)
                                <img src="{{ Storage::url($purchaseResource->thumbnail) }}"
                                    alt="{{ $purchaseResource->title }}" class="w-20 h-20 object-cover rounded-lg">
                            @endif
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">
                                    {{ $purchaseResource->title }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $purchaseResource->author }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Price</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    Ksh{{ number_format($purchaseResource->price) }}
                                </span>
                            </div>

                            <div class="hide flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Quantity</span>
                                <div class="flex items-center gap-2">
                                    <button wire:click="$set('purchaseQuantity', max(1, $purchaseQuantity - 1))"
                                        class="p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="w-8 text-center font-medium">{{ $purchaseQuantity }}</span>
                                    <button wire:click="$set('purchaseQuantity', $purchaseQuantity + 1)"
                                        class="p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                <div class="flex justify-between font-medium">
                                    <span>Total</span>
                                    <span class="text-lg text-primary-600 dark:text-primary-400">
                                        Ksh{{ number_format($purchaseResource->price * $purchaseQuantity) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="agreeTerms"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                I agree to the 
                                <a href="/terms" class="text-primary-600 hover:text-primary-700">Terms of Service</a> and 
                                <a href="/privacy"lass="text-primary-600 hover:text-primary-700">Privacy Policy</a>
                            </span>
                        </label>
                        @error('agreeTerms')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <button wire:click="processPurchase" wire:loading.attr="disabled"
                        class="w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 disabled:bg-primary-400 text-white font-medium rounded-lg transition">
                        <span wire:loading.remove>Proceed to Checkout</span>
                        <span wire:loading>Processing...</span>
                    </button>

                    <p class="mt-3 text-xs text-center text-gray-500 dark:text-gray-400">
                        Secure payment processed via M-PESA
                    </p>
                </div>
            </div>
        </div>
    @endif
