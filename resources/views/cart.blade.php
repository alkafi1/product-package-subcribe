@extends('layout')

@section('main-content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Cart Details</h4>
                    </div>
                    <div class="card-body">
                        {{-- Cart details displayed as cards --}}
                        <div class="row">
                            @forelse ($products as $product)
    <div class="col-md-3 mb-4">
        <form action="" method="POST" class="product-form">
            @csrf
            <div class="card shadow-sm border">
                <div class="card-body">
                    <h5 class="card-title">{{ $product->productDetails->name }}</h5>

                    {{-- Product Price --}}
                    <p class="card-text">
                        <strong>Price:</strong> ৳{{ number_format($product->product_price, 2) }}
                    </p>

                    {{-- Quantity input based on Purchase Type --}}
                    <p class="card-text">
                        <strong>Quantity:</strong>
                        @if ($product->purchase_type == 'buy-now')
                            <input type="number" value="{{ $product->product_quantity }}"
                                name="buy-now-quantity" id="buy-now-quantity-{{ $product->product_id }}"
                                class="form-control quantity-input"
                                data-unit-price="{{ $product->product_price }}"
                                data-id="{{ $product->id }}">
                        @elseif ($product->purchase_type == 'schedule-buy')
                            <input type="number" value="{{ $product->product_quantity }}"
                                name="schedule-buy-quantity"
                                id="schedule-buy-quantity-{{ $product->product_id }}"
                                class="form-control quantity-input"
                                data-unit-price="{{ $product->product_price }}"
                                data-id="{{ $product->id }}">
                        @else
                            {{ $product->product_quantity }}
                        @endif
                    </p>

                    {{-- Total Price (updates dynamically based on quantity change) --}}
                    <p class="card-text">
                        <strong>Total Price:</strong>
                        ৳<span id="total-price-{{ $product->id }}">{{ number_format($product->product_total_price, 2) }}</span>
                    </p>

                    {{-- Purchase Type Dropdown --}}
                    <p class="card-text">
                        <strong>Purchase Type:</strong>
                        <select name="purchase_type" id="purchase-type-{{ $loop->index }}"
                            class="form-select">
                            <option value="buy-now" {{ $product->purchase_type == 'buy-now' ? 'selected' : '' }}>
                                One-time Buy</option>
                            <option value="schedule-buy" {{ $product->purchase_type == 'schedule-buy' ? 'selected' : '' }}>
                                Scheduled Buy</option>
                            <option value="bulk" {{ $product->purchase_type == 'bulk' ? 'selected' : '' }}>
                                Bulk One-Time Purchase</option>
                        </select>
                    </p>

                    {{-- Schedule Type (only for scheduled buys) --}}
                    @if ($product->schedule_type)
                        <p class="card-text">
                            <strong>Schedule Type:</strong>
                            <select class="form-select schedule_type" name="schedule_type">
                                <option value="monthly" {{ $product->schedule_type == 'monthly' ? 'selected' : '' }}>
                                    Monthly
                                </option>
                                <option value="days" {{ $product->schedule_type == 'days' ? 'selected' : '' }}>
                                    Days
                                </option>
                            </select>
                        </p>
                    @endif

                    {{-- Bulk Purchase Details --}}
                    @if ($product->purchase_type == 'bulk')
                        <p class="card-text">
                            <strong>Purchase Details:</strong>
                            @php
                                $bundleDetails = json_decode($product->productDetails->bundle_details);
                            @endphp
                            <select name="bulk-details" id="bulk-details-{{ $loop->index }}"
                                class="form-select">
                                @foreach ($bundleDetails as $index => $bundle)
                                    @php
                                        $totalOriginalPrice = $bundle->product_price * $bundle->quantity;
                                        if ($bundle->type == 'percentage') {
                                            $percentageSaved = $bundle->discount_amount;
                                            $savings = $totalOriginalPrice - $bundle->after_discount;
                                        } elseif ($bundle->type == 'fixed') {
                                            $savings = $bundle->discount_amount * $bundle->quantity;
                                            $percentageSaved = round(($savings / $totalOriginalPrice) * 100);
                                        }
                                    @endphp
                                    <option value="{{ $bundle->quantity }}"
                                        {{ $product->product_quantity == $bundle->quantity ? 'selected' : '' }}>
                                        Save {{ $percentageSaved }}% - Buy {{ $bundle->quantity }}
                                        for ৳{{ number_format($bundle->after_discount, 2) }}
                                        (original price ৳{{ number_format($totalOriginalPrice, 2) }} each, saving
                                        ৳{{ number_format($savings, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </p>
                    @endif

                    {{-- Schedule Purchase Details --}}
                    @if ($product->purchase_type == 'schedule-buy')
                        <p class="card-text">
                            <strong>Purchase Details:</strong>
                            <select name="schedule-details"
                                id="schedule-details-{{ $loop->index }}" class="form-select">
                                @if ($product->productDetails->schedule_type === 'monthly')
                                    @foreach (json_decode($product->productDetails->schedule, true) as $schedule)
                                        <option value="{{ $schedule['interval'] }}">
                                            Every {{ $schedule['interval'] }} month (Day {{ $schedule['day'] }})
                                        </option>
                                    @endforeach
                                @elseif ($product->productDetails->schedule_type === 'days')
                                    @foreach (json_decode($product->productDetails->schedule, true) as $schedule)
                                        <option value="{{ $schedule['interval'] }}">
                                            Every {{ $schedule['interval'] }} day(s) (Day {{ $schedule['day'] }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </p>
                    @endif
                </div>

                {{-- Card Footer with Action Buttons --}}
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('products.show', $product->product_id) }}" class="bg-light">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="" class="bg-light">
                        <i class="fas fa-wrench text-success"></i>
                    </a>
                    <a href="{{ route('products.cart.delete', $product->id) }}" class="bg-light">
                        <i class="fas fa-trash-alt text-danger"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
@empty
    <p>There is NO Product in cart</p>
@endforelse


                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="" class="btn btn-success">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            $(document).ready(function() {
                $('.quantity-input').on('input', function() {
                    var quantity = $(this).val();
                    var unitPrice = $(this).data('unit-price');
                    var Id = $(this).data('id');
                    var totalPrice = unitPrice * quantity;
                    $('#total-price-' + Id).text(totalPrice.toFixed(2));
                });

            });
        </script>
    @endpush
@endsection
