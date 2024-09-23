@extends('layout')

@section('main-content')
    
    <h2 class="mb-4">Buy and Enjoy</h2>
    <div id="product-container" class="row">
        @foreach ($products as $product)
            <div class="col-md-4 mb-4">
                <div class="product-card border rounded shadow-sm overflow-hidden">
                    <div class="product-image">
                       <a href="{{ route('products.show', ['product' => $product->id]) }}"><img src="https://via.placeholder.com/300" alt="{{ $product->name }}" class="img-fluid"></a> 
                    </div>
                    <div class="p-3">
                        <h5 class="product-title">{{ $product->name }}</h5>
                        <p class="product-price text-success">৳{{ number_format($product->price, 2) }}</p>
                        <p class="product-description">{{ $product->description ?? 'No description available.' }}</p>
                        
                        <a href="{{ route('products.show', ['product' => $product->id]) }}" class="btn btn-buy w-100">View
                            Details</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div id="pagination-container" class="mt-4">
        {{ $products->links() }}
    </div>
@endsection
