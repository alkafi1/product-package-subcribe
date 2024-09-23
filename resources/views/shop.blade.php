@extends('layout')

@section('main-content')
    <h2 class="mb-4">Buy and Enjoy</h2>
    <div id="product-container" class="row">
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
                            @if ($product->is_subscribable)
                                <span class="badge bg-info">Subscribable</span>
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

    <div id="pagination-container" class="mt-4">
        {{ $products->links() }}
    </div>
@endsection
