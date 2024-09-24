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
                                                    <strong>Price:</strong> ৳ <span
                                                        id="">{{ number_format($product->product_price, 2) }}</span>
                                                </p>

                                                {{-- Quantity input based on Purchase Type --}}
                                                <p class="card-text q-quantity">
                                                    {{-- Quantity Display --}}

                                                    <strong>Quantity:</strong>
                                                    @if ($product->purchase_type == 'buy-now')
                                                        <input type="number" value="{{ $product->product_quantity }}"
                                                            name="buy-now-quantity"
                                                            id="buy-now-quantity-{{ $product->product_id }}"
                                                            class="form-control quantity-input quantity-display"
                                                            data-unit-price="{{ $product->product_price }}"
                                                            data-id="{{ $product->id }}"
                                                            data-quantity="{{ $product->product_quantity }}">
                                                    @elseif ($product->purchase_type == 'schedule-buy')
                                                        <input type="number" value="{{ $product->product_quantity }}"
                                                            name="schedule-buy-quantity quantity-display"
                                                            id="schedule-buy-quantity-{{ $product->product_id }}"
                                                            class="form-control quantity-input"
                                                            data-unit-price="{{ $product->product_price }}"
                                                            data-id="{{ $product->id }}"
                                                            data-quantity="{{ $product->product_quantity }}">
                                                    @else
                                                        <span
                                                            class="quantity-input quantity-display bulk-now-quantity-{{ $product->product_id }}"
                                                            data-unit-price="{{ $product->product_price }}"
                                                            data-quantity="{{ $product->product_quantity }}"
                                                            data-id="{{ $product->id }}">{{ $product->product_quantity }}</span>
                                                    @endif
                                                </p>

                                                {{-- Total Price (updates dynamically based on quantity change) --}}
                                                <p class="card-text">
                                                    <strong>Total Price:</strong>
                                                    ৳<span
                                                        id="total-price-{{ $product->id }}">{{ number_format($product->product_total_price, 2) }}</span>
                                                </p>

                                                {{-- Purchase Type Dropdown --}}
                                                <p class="card-text">
                                                    <strong>Purchase Type:</strong>
                                                    <select name="purchase_type" id="purchase-type-{{ $product->id }}"
                                                        data-unit-price="{{ $product->product_price }}"
                                                        data-id="{{ $product->id }}"
                                                        data-quantity="{{ $product->product_quantity }}"
                                                        class="form-select purchase_type">
                                                        <option value="buy-now"
                                                            {{ $product->purchase_type == 'buy-now' ? 'selected' : '' }}>
                                                            One-time Buy
                                                        </option>
                                                        @if ($product->productDetails->is_subscribable)
                                                            <option value="schedule-buy"
                                                                {{ $product->purchase_type == 'schedule-buy' ? 'selected' : '' }}>
                                                                Scheduled Buy
                                                            </option>
                                                        @endif
                                                        @if ($product->productDetails->is_bundle)
                                                            <option value="bulk"
                                                                {{ $product->purchase_type == 'bulk' ? 'selected' : '' }}>
                                                                Bulk One-Time Purchase
                                                            </option>
                                                        @endif
                                                    </select>
                                                </p>

                                                {{-- Schedule Type (only for scheduled buys) --}}
                                                @if ($product->schedule_type)
                                                    <p class="card-text">
                                                        <strong>Schedule Type:</strong>
                                                        <select class="form-select schedule_type" name="schedule_type">
                                                            <option value="monthly"
                                                                {{ $product->schedule_type == 'monthly' ? 'selected' : '' }}>
                                                                Monthly
                                                            </option>
                                                            <option value="days"
                                                                {{ $product->schedule_type == 'days' ? 'selected' : '' }}>
                                                                Days
                                                            </option>
                                                        </select>
                                                    </p>
                                                @endif

                                                {{-- Bulk Purchase Details --}}
                                                <!-- Purchase details section (hidden by default) -->
                                                <div id="bulk-details-container-{{ $product->id }}">
                                                    @if ($product->purchase_type == 'bulk')
                                                        <p class="card-text">
                                                            <strong>Purchase Details:</strong>
                                                            @php
                                                                $bundleDetails = json_decode(
                                                                    $product->productDetails->bundle_details,
                                                                );
                                                            @endphp
                                                            <select name="bulk-details"
                                                                id="bulk-details-{{ $product->id }} "
                                                                class="form-select bulk-option"
                                                                data-id="{{ $product->id }}">
                                                                @foreach ($bundleDetails as $index => $bundle)
                                                                    @php
                                                                        $totalOriginalPrice =
                                                                            $bundle->product_price * $bundle->quantity;
                                                                        if ($bundle->type == 'percentage') {
                                                                            $percentageSaved = $bundle->discount_amount;
                                                                            $savings =
                                                                                $totalOriginalPrice -
                                                                                $bundle->after_discount;
                                                                        } elseif ($bundle->type == 'fixed') {
                                                                            $savings =
                                                                                $bundle->discount_amount *
                                                                                $bundle->quantity;
                                                                            $percentageSaved = round(
                                                                                ($savings / $totalOriginalPrice) * 100,
                                                                            );
                                                                        }
                                                                    @endphp
                                                                    <option value="{{ $bundle->quantity }}"
                                                                        {{ $product->product_quantity == $bundle->quantity ? 'selected' : '' }}>
                                                                        Save {{ $percentageSaved }}% - Buy
                                                                        {{ $bundle->quantity }} for
                                                                        ৳{{ number_format($bundle->after_discount, 2) }}
                                                                        (original price
                                                                        ৳{{ number_format($totalOriginalPrice, 2) }} each,
                                                                        saving ৳{{ number_format($savings, 2) }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </p>
                                                    @endif
                                                </div>

                                                {{-- Schedule Purchase Details --}}
                                                @if ($product->purchase_type == 'schedule-buy')
                                                    <p class="card-text">
                                                        <strong>Purchase Details:</strong>
                                                        <select name="schedule-details"
                                                            id="schedule-details-{{ $product->id }}" class="form-select">
                                                            @if ($product->productDetails->schedule_type === 'monthly')
                                                                @foreach (json_decode($product->productDetails->schedule, true) as $schedule)
                                                                    <option value="{{ $schedule['interval'] }}">
                                                                        Every {{ $schedule['interval'] }} month (Day
                                                                        {{ $schedule['day'] }})
                                                                    </option>
                                                                @endforeach
                                                            @elseif ($product->productDetails->schedule_type === 'days')
                                                                @foreach (json_decode($product->productDetails->schedule, true) as $schedule)
                                                                    <option value="{{ $schedule['interval'] }}">
                                                                        Every {{ $schedule['interval'] }} day(s) (Day
                                                                        {{ $schedule['day'] }})
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </p>
                                                @endif
                                            </div>

                                            {{-- Card Footer with Action Buttons --}}
                                            <div class="card-footer d-flex justify-content-between">
                                                <a href="{{ route('products.show', $product->product_id) }}"
                                                    class="bg-light">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="" class="bg-light">
                                                    <i class="fas fa-wrench text-success"></i>
                                                </a>
                                                <a href="{{ route('products.cart.delete', $product->id) }}"
                                                    class="bg-light">
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
                //chnage quantity live chnage price
                $('.quantity-input').on('input', function() {
                    var quantity = $(this).val();
                    var unitPrice = $(this).data('unit-price');
                    var Id = $(this).data('id');
                    var totalPrice = unitPrice * quantity;
                    $('#total-price-' + Id).text(totalPrice.toFixed(2));
                });
                //chnage purchase type
                $('.purchase_type').on('change', function() {
                    const selectedValue = $(this).val();
                    const unitPrice = parseFloat($(this).data('unit-price')); // Convert to a number
                    const Id = $(this).data('id'); // Get the product ID from the dropdown
                    const quantity = $(this).data('quantity');
                    // Find the quantity display span (specific to this product's card)
                    const quantityDisplay = $(this).closest('.card-body').find('.quantity-display');

                    let inputField;

                    if (selectedValue === 'buy-now' || selectedValue === 'schedule-buy') {
                        // Create an input field for 'buy-now' or 'schedule-buy'
                        inputField = `
                            <input type="number" value="1" 
                                name="${selectedValue}-quantity" 
                                id="${selectedValue}-quantity-${Id}" 
                                class="form-control quantity-input quantity-display" 
                                data-unit-price="${unitPrice}" 
                                data-id="${Id}" data-quantity="${quantity}" min="1">
                        `;

                        // Update total price when input is shown
                        if ($('#total-price-' + Id).length) {
                            $('#total-price-' + Id).text(unitPrice.toFixed(2));
                        }

                        // Listen for input change on quantity input
                        $(document).on('input', `#${selectedValue}-quantity-${Id}`, function() {
                            const newQuantity = $(this).val();
                            const newTotalPrice = (unitPrice * newQuantity).toFixed(2);
                            $('#total-price-' + Id).text(newTotalPrice);
                        });

                        // Hide bulk details
                        $('#bulk-details-container-' + Id).hide();

                    } else {

                        $.ajax({
                            url: '{{ route('cart.bundle.details') }}', // The URL to send the request to
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}', // Include the CSRF token for security
                                id: Id,
                            },
                            success: function(response) {

                                // Handle success response
                                if (response.success) {
                                    console.log(response.inputField);
                                    $(quantityDisplay).replaceWith($(response.inputField));
                                    $('#total-price-' + Id).text(response.totalPrice);
                                    $('#bulk-details-container-' + Id).show();
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                // console.error(xhr.responseText);
                                toastr.error('Failed to update bundle details for product ID: ' +
                                    productId + '. Please try again.');
                            }
                        });
                    }
                    // Replace the quantity display with the inputField or span
                    $(quantityDisplay).replaceWith($(inputField));
                });
                //sedn ajax request for get bundle detail
                $('.bulk-option').on('change', function() {
                    let quantity = $(this).val(); // Get the selected value (quantity)
                    let Id = $(this).data('id'); // Extract the product ID from the select element's ID
                    // alert(Id);
                    // Send an AJAX request
                    $.ajax({
                        url: '{{ route('products.bundle.details') }}', // The URL to send the request to
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}', // Include the CSRF token for security
                            id: Id,
                            quantity: quantity
                        },
                        success: function(response) {
                            // Handle success response
                            if (response.success) {
                                var quantitySpan = $('.bulk-now-quantity-' + response.id);
                                var totalPriceSpan = $('#total-price-' + response.id);
                                totalPriceSpan.text(response.bundle.after_discount);
                                quantitySpan.text(response.bundle.quantity);
                                quantitySpan.attr('data-unit-price', response.bundle.unit_price);
                                quantitySpan.attr('data-quantity', response.bundle.quantity);

                            } else {
                                toastr.success(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle error
                            console.error(xhr.responseText);
                            alert('Failed to update bundle details for product ID: ' + productId +
                                '. Please try again.');
                        }
                    });
                });



            });
        </script>
    @endpush
@endsection
