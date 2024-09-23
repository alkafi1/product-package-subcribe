@extends('layout')

@section('main-content')
    <div class="row">
        <!-- Product Image Section -->
        <div class="col-md-6 product-image">
            <img src="https://via.placeholder.com/600" alt="{{ $product->name }}">
        </div>
        <!-- Product Details Section -->
        <div class="col-md-6">
            <form action="{{ route('products.order') }}" method="post">
                @csrf
                <h2 class="product-title">{{ $product->name }}</h2>
                <p class="product-price">৳{{ number_format($product->price, 2) }}</p>
                <input type="hidden" name="product_price" value="{{ $product->price }}">
                <p class="text-muted">SKU: #{{ $product->slug }}</p>

                <!-- Purchase Options -->
                <h5>Select type of purchase:</h5>
                <div class="purchase-option active">
                    <input type="radio" id="buy-now-option" name="purchase-type" value="buy-now" checked>
                    <label for="buy-now-option" class="d-flex align-items-center">
                        <i class="bi bi-cart"></i> One Time Purchase
                    </label>
                    <div class="d-flex align-items-center mb-3 mt-2 quantity-container" id="quantity-container">
                        <label for="quantity" class="me-3">Quantity:</label>
                        <input type="number" id="quantity" name="buy-now-quantity"
                            class="form-control quantity {{ $errors->has('buy-now-quantity') ? 'is-invalid' : '' }}"
                            value="{{ old('buy-now-quantity', 1) }}" min="1">
                    </div>
                </div>

                @if ($product->is_subscribable)
                    <div class="purchase-option">
                        <input type="radio" id="schedule-buy-option" name="purchase-type" value="schedule-buy">
                        <label for="schedule-buy-option" class="d-flex align-items-center">
                            <i class="bi bi-calendar4-week"></i> Schedule Buy
                        </label>
                        <div class="schedule-select mt-2" style="display: none;">
                            <label for="schedule-frequency" class="form-label">Choose frequency:</label>
                            <select id="schedule-frequency"
                                class="form-select {{ $errors->has('schedule-interval') ? 'is-invalid' : '' }}"
                                name="schedule-interval">
                                @if ($product->is_subscribable)
                                    @if ($product->schedule_type === 'monthly')
                                        @foreach (json_decode($product->schedule, true) as $schedule)
                                            <option value="{{ $schedule['interval'] }}">
                                                Every {{ $schedule['interval'] }} month (Day {{ $schedule['day'] }})
                                            </option>
                                        @endforeach
                                    @elseif ($product->schedule_type === 'days')
                                        @foreach (json_decode($product->schedule, true) as $schedule)
                                            <option value="{{ $schedule['interval'] }}">
                                                Every {{ $schedule['interval'] }} day(s) (Day {{ $schedule['day'] }})
                                            </option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>

                            @if ($errors->has('schedule-interval'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('schedule-interval') }}
                                </div>
                            @endif

                            <div class="d-flex align-items-center mb-3 mt-2 quantity-container">
                                <label for="schedule-quantity" class="me-3">Quantity:</label>
                                <input type="number" id="schedule-quantity" name="schedule-quantity"
                                    class="form-control quantity {{ $errors->has('schedule-quantity') ? 'is-invalid' : '' }}"
                                    value="{{ old('schedule-quantity', 1) }}" min="1">

                                @if ($errors->has('schedule-quantity'))
                                    <span class="text-danger">{{ $errors->first('schedule-quantity') }}</span>
                                @endif
                            </div>
                        </div>

                    </div>
                @endif

                <!-- Bulk One-Time Options -->
                @if ($product->is_bundle)
                    <div class="purchase-option">
                        <input type="radio" id="bulk-option" name="purchase-type" value="bulk">
                        <label for="bulk-option" class="d-flex align-items-center">
                            <i class="bi bi-box"></i> Bulk One-Time Purchase
                        </label>
                        @php
                            // Sample bundle_details data (you can replace it with your actual product data)
                            $bundleDetails = json_decode($product->bundle_details);
                        @endphp

                        @if ($bundleDetails)
                            <div class="bulk-options mt-2" style="display: none;">
                                @foreach ($bundleDetails as $index => $bundle)
                                    @php
                                        // Calculate the total original price for the bundle
                                        $totalOriginalPrice = $bundle->product_price * $bundle->quantity;

                                        // For percentage discounts, calculate the amount saved
                                        if ($bundle->type == 'percentage') {
                                            $percentageSaved = $bundle->discount_amount; // e.g. 10% saved
                                            $savings = $totalOriginalPrice - $bundle->after_discount;
                                        } elseif ($bundle->type == 'fixed') {
                                            // For fixed discounts, calculate the amount saved directly
                                            $savings = $bundle->discount_amount * $bundle->quantity;
                                            $percentageSaved = round(($savings / $totalOriginalPrice) * 100); // Calculate percentage saved for display
                                        }
                                    @endphp
                                    <div class="bulk-option-card">
                                        <input type="radio" id="bulk-{{ $index + 1 }}" name="bundle-quantity"
                                            value="{{ $bundle->quantity }}">
                                        <label for="bulk-{{ $index + 1 }}">
                                            Save {{ $percentageSaved }}% - Buy {{ $bundle->quantity }} for
                                            ৳{{ number_format($bundle->after_discount, 2) }}
                                            (original price ৳{{ number_format($totalOriginalPrice, 2) }} each, saving
                                            ৳{{ number_format($savings, 2) }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                @endif
                {{-- <!-- Buy Button -->
            <form class="mt-4"> --}}
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="btn btn-buy btn-lg w-100">Proceed</button>
            </form>

            <!-- Additional Information -->
            <div class="mt-4">
                <p><strong>Category:</strong> Health & Beauty</p>
                <p><strong>Tags:</strong> <a href="#">Vitamins</a>, <a href="#">Supplements</a></p>
            </div>

            <!-- Share Icons -->
            <div class="mt-3">
                <span class="me-2">Share:</span>
                <a href="#" class="me-2"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-twitter"></i></a>
            </div>
        </div>
    </div>
    <h4 class="mt-5 mb-4">Related Products</h4>
    <div class="row">
        @foreach ($products as $product)
            <div class="col-md-3 mb-4">
                <div class="product-card border rounded shadow-sm overflow-hidden h-100 d-flex flex-column">
                    <div class="product-image">
                        <a href="{{ route('products.show', ['product' => $product->id]) }}">
                            <img src="https://via.placeholder.com/300" alt="{{ $product->name }}" class="img-fluid w-100"
                                style="height: 200px; object-fit: cover;">
                        </a>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h5 class="product-title">{{ $product->name }}</h5>

                        <!-- Badges for is_bundle and is_subscription -->
                        <div class="mb-2">
                            @if ($product->is_bundle)
                                <span class="badge bg-warning">Bundle</span>
                            @endif
                            @if ($product->is_subscription)
                                <span class="badge bg-info">Subscription</span>
                            @endif
                        </div>

                        <p class="product-price text-success">৳{{ number_format($product->price, 2) }}</p>
                        <p class="product-description flex-grow-1">
                            {{ $product->description ?? 'No description available.' }}</p>

                        <a href="{{ route('products.show', ['product' => $product->id]) }}"
                            class="btn btn-buy w-100 mt-auto">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
    @push('script')
    @endpush


@endsection
