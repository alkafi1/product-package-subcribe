<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-image img {
            max-width: 100%;
            height: auto;
        }

        .product-price {
            font-size: 24px;
            font-weight: bold;
        }

        .product-description {
            margin-top: 20px;
        }

        .quantity {
            width: 70px;
        }

        .btn-buy {
            background-color: #28a745;
            color: white;
            transition: border-color 0.3s ease;
            border: 2px solid transparent;
            /* Default border */
        }

        .btn-buy:hover {
            border-color: #28a745;
            /* Green border on hover */
            background-color: transparent;
            /* Optional: make background transparent on hover */
            color: #28a745;
            /* Change text color to green on hover */
        }

        .purchase-option {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .purchase-option:hover {
            background-color: #f9f9f9;
        }

        .purchase-option input[type="radio"] {
            display: none;
            /* Hide radio buttons */
        }

        .purchase-option label {
            display: block;
            padding: 10px;
            /* Space for clickable area */
        }

        .purchase-option.active {
            border: 2px solid #28a745;
            /* Green border for the entire card */
        }

        .bulk-option-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            position: relative;
            cursor: pointer;
            transition: box-shadow 0.3s ease;
        }

        .bulk-option-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .bulk-option-card input[type="radio"] {
            display: none;
            /* Hide radio buttons */
        }

        .bulk-option-card label {
            display: block;
            padding: 10px;
            /* Space for clickable area */
        }

        .bulk-option-card input[type="radio"]:checked+label {
            border: 2px solid #28a745;
            /* Green border for selected bulk option */
            background-color: #f9f9f9;
            /* Optional: background color for selected */
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 20px;
            transition: box-shadow 0.3s ease, border-color 0.3s ease;
        }

        .product-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-color: #28a745;
            /* Green border on hover */
        }

        .product-image img {
            width: 100%;
            height: auto;
            object-fit: cover;
            /* Makes the image scale properly */
            border-bottom: 1px solid #ddd;
        }

        .product-title {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }

        .product-price {
            font-size: 16px;
            color: #28a745;
            margin-bottom: 10px;
        }

        .product-description {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .rating {
            font-size: 14px;
            color: #ffc107;
            /* Gold color for stars */
        }

        .btn-buy {
            background-color: #28a745;
            color: white;
            border: 1px solid transparent;
            transition: border-color 0.3s ease;
        }

        .btn-buy:hover {
            border-color: #28a745;
            /* Green border on hover */
        }

        .nav-link {
            color: #fff !important;
        }

        /* Set the height of the carousel images */
        .carousel-image {
            height: 500px;
            object-fit: cover;
        }

        /* Background animation for the section */
        .animated-section {
            height: 50px;
            padding-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(270deg, #ffafbd, #ffc3a0, #ffafbd);
            background-size: 600% 600%;
            animation: backgroundAnimation 6s ease infinite;
            border: 5px solid transparent;
            /* Initial transparent border */
            border-radius: 5px;
            /* Optional: add rounded corners */
            position: relative;
            /* Positioning for the inner border effect */
        }

        /* Background animation for the section */
        .animated-section {
            height: 50px;
            padding-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('https://c.myholidays.com/blog/blog/content/images/2020/09/Shopping-In-Qatar-The-Top-10-Souvenirs-To-Buy-From-Qatar.webp');
            /* Set your graphical image here */
            background-size: cover;
            /* Cover the entire section */
            background-position: center;
            /* Center the image */
            position: relative;
            overflow: hidden;
            /* Ensure the border stays within bounds */
        }

        /* Inner border effect */
        .animated-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 5px solid transparent;
            /* Initial transparent border */
            border-radius: 5px;
            z-index: -1;
            /* Place behind the text */
            animation: borderAnimation 6s linear infinite;
            /* Border animation */
            background-image: linear-gradient(0deg, #ffafbd, #ffc3a0, #ffafbd);
            /* Gradient for color rotation */
            background-size: 200% 200%;
            /* Ensure the gradient moves */
        }

        /* Text inside the animated background */
        .animated-section h2 {
            color: white;
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
        }

        /* Keyframes for border animation with rotation */
        @keyframes borderAnimation {
            0% {
                transform: rotate(0deg);
                background-position: 0% 50%;
            }

            25% {
                transform: rotate(90deg);
                background-position: 100% 50%;
            }

            50% {
                transform: rotate(180deg);
                background-position: 100% 50%;
            }

            75% {
                transform: rotate(270deg);
                background-position: 0% 50%;
            }

            100% {
                transform: rotate(360deg);
                background-position: 0% 50%;
            }
        }
    </style>
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
