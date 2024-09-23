@extends('layout')

@section('main-content')
    <!-- Banner Slider -->
    <div id="bannerCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwzNjUyOXwwfDF8c2VhcmNofDF8fG5hdHVyZXxlbnwwfHx8fDE2Nzk0MTc1NjY&ixlib=rb-4.0.3&q=80&w=1080"
                    class="d-block w-100 carousel-image" alt="Banner 1">
            </div>
            <div class="carousel-item">
                <img src="https://images.pexels.com/photos/338504/pexels-photo-338504.jpeg?auto=compress&cs=tinysrgb&w=1600&h=500&dpr=1"
                    class="d-block w-100 carousel-image" alt="Banner 2">
            </div>
            <div class="carousel-item">
                <img src="https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_960_720.jpg"
                    class="d-block w-100 carousel-image" alt="Banner 3">
            </div>
        </div>

        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Animated Background Section -->
    <div class="animated-section mb-3">
        <h2 class="mb-4">Buy and Enjoy</h2>
    </div>
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

    <div id="pagination-container" class="mt-2">
        {{ $products->links() }}
    </div>
@endsection
