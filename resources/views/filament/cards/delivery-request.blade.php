@php $record = $getRecord(); @endphp

<div x-data="{ open: false }" class="w-full">
    <div class="mb-2">
        <!-- Left Column -->
        <div>
            <div class="flex justify-between">
                <div>
                    <span class="mb-4 inline-block justify-end px-3 py-2 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800">
                        {{ $record->matches()->count() }} Carry Offer(s)
                    </span>
                </div>

            </div>
            <span class="mb-4 inline-block px-0 py-2 text-xs font-bold text-yellow-800">
                    Weight: {{ $record->weight }} KG <br/>
                    Price: {{ $record->price }}
                </span>


            <div class="flex mb-5 justify-between">
                <div>
                    <i class="fas fa-plane-departure text-white mr-2"></i>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $record->fromCity->name }}
                    </h2>
                    <p class="mt-1 text-sm">
                        {{ $record->fromCity->country->name }}
                    </p>
                </div>

                <div>
                    <i class="fas fa-plane-arrival text-white mr-2"></i>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $record->toCity->name }}
                    </h2>
                    <p class="mt-1 text-sm">
                        {{ $record->toCity->country->name }}
                    </p>
                </div>
            </div>

            <div class="">
                <p class="text-sm font-bold text-gray-400 items-center">
                    Parcel Requested Between:
                </p>

                <p class="mt-2 text-sm font-black text-white items-center">
                    {{ \Carbon\Carbon::parse($record->preferred_date)->format('d M Y') }} -> {{ \Carbon\Carbon::parse($record->delivery_deadline)->format('d M Y') }}
                </p>
            </div>
        </div>

    </div>

    @if($record->products()->count() > 0)
        <!-- Product Detail Toggle Button -->
        <div class="mt-4 mb-2">

            <div class="mt-4 flex gap-2 mb-3">
                <x-filament::button
                    @click="open = !open"
                    color="primary"
                    icon="heroicon-o-globe-asia-australia"
                    x-text="open ? 'Hide' : 'Item to Buy'"
                >
                    Item to buy
                </x-filament::button>
            </div>
            <!-- Collapsible Product Details Section -->
            <div x-show="open" x-transition class="mt-4 text-sm text-gray-700 dark:text-gray-300">
                <p><strong>Product Name:</strong> {{ $record->products()->first()?->product_name }}</p>
                <p><strong>Product Link:</strong>
                    @if ($link = $record->products()->first()?->product_link)
                        <a href="{{ $link }}" target="_blank" class="text-primary-600 hover:underline">Link</a>
                    @else
                        <span class="text-gray-400">N/A</span>
                    @endif
                </p>
                <p><strong>Product Description:</strong> {{ $record->products()->first()?->product_description }}</p>
                <p><strong>Price (Approx):</strong> {{ $record->products()->first()?->price }}</p>
                <p><strong>Store Name:</strong> {{ $record->products()->first()?->store_name }}</p>
                <p><strong>Store Location:</strong> {{ $record->products()->first()?->store_location }}</p>
            </div>
        </div>
    @endif
</div>
