<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Character Encoding -->
    <meta charset="UTF-8">

    <!-- Viewport for Responsive Design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Title (shown in browser tab) -->
    <title>Your Page Title</title>

    <!-- Description for SEO -->
    <meta name="description" content="Brief description of your page for SEO purposes.">

    <!-- Keywords for SEO (optional, not widely used by search engines anymore) -->
    <meta name="keywords" content="Keyword1, Keyword2, Keyword3">

    <!-- Author Info -->
    <meta name="author" content="Your Name or Company Name">

    <!-- Robots Meta Tag for SEO -->
    <meta name="robots" content="index, follow">

    <!-- Open Graph (OG) Tags for Social Sharing -->
    <meta property="og:title" content="Your Page Title">
    <meta property="og:description" content="Brief description of your page for social sharing.">
    <meta property="og:image" content="URL-to-your-image.jpg">
    <meta property="og:url" content="https://yourwebsite.com/page">
    <meta property="og:type" content="website">

    <!-- Twitter Card Tags for Twitter Sharing -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Your Page Title">
    <meta name="twitter:description" content="Brief description of your page for Twitter.">
    <meta name="twitter:image" content="URL-to-your-image.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('\public\assets\css\style.css') }}">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('products.index') }}">ProductStore</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.cart.show') }}">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.create') }}">Add Products</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        @yield('main-content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Toastr CSS and JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <script>
        $(document).ready(function() {
            // Show/hide options based on purchase type selection
            $('input[name="purchase-type"]').on('change', function() {
                $('.schedule-select').hide();
                $('.bulk-options').hide();
                $('#quantity').show(); // Show quantity for Buy Now
                $('#quantity-container').show(); // Show quantity for Buy Now

                if ($(this).val() === 'schedule-buy') {
                    $('.schedule-select').show();
                    $('#quantity').hide(); // Hide quantity for Schedule Buy
                    $('#quantity-container').hide(); // Hide quantity for Schedule Buy
                } else if ($(this).val() === 'bulk') {
                    $('.bulk-options').show();
                    $('#quantity').hide(); // Hide quantity for Bulk Buy
                    $('#quantity-container').hide(); // Hide quantity for Bulk Buy
                }
            });

            // Active class for bulk options
            $('input[name="bulk-amount"]').on('change', function() {
                $('.bulk-option-card').removeClass('active');
                $(this).closest('.bulk-option-card').addClass('active');
            });

            // Add active class to purchase options
            $('input[name="purchase-type"]').on('change', function() {
                $('.purchase-option').removeClass('active');
                $(this).closest('.purchase-option').addClass('active');
            });
        });
    </script>
    @stack('script')
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "timeOut": "3000", // 3 seconds
            "extendedTimeOut": "1000",
        };

        // Display success message if available
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        // Display error messages if any exist
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    </script>

</body>

</html>
