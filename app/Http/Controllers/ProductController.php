<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCart;
use App\Models\ProductOrder;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function index()
    {

        $products = Product::latest()->paginate(8); // Adjust the number per page as needed
        return view('shop', compact('products'));
    }
    public function home()
    {

        $products = Product::latest()->paginate(8); // Adjust the number per page as needed
        return view('shop', compact('products'));
    }


    public function create()
    {
        return view('create');
    }


    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'is_bundle' => 'nullable',
            'is_subscribable' => 'nullable',
        ];

        if ($request->input('is_bundle')) {
            $rules['bundle_quantity'] = 'required|array';
            $rules['bundle_quantity.*'] = 'required|integer|min:1';
            $rules['bundle_discount_type'] = 'required|array';
            $rules['bundle_discount_type.*'] = 'required|in:percentage,fixed';
            $rules['bundle_discount_amount'] = 'required|array';
            $rules['bundle_discount_amount.*'] = 'required|numeric|min:0';
        }

        if ($request->input('is_subscribable')) {
            $rules['schedule_type'] = 'required|in:monthly,days';
            $rules['schedule_interval'] = 'required|array';
            $rules['schedule_interval.*'] = 'required|integer|min:1';
            $rules['schedule_day'] = 'required|array';
            $rules['schedule_day.*'] = 'required|integer|min:0|max:6';
            $rules['schedule_time'] = 'required|array';
            $rules['schedule_time.*'] = 'required|date_format:H:i'; // Ensure time format
        }

        $validator = Validator::make($request->all(), $rules);

        // Return with errors if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $bundleDetails = [];
        if ($request->has('is_bundle')) {
            foreach ($request->bundle_quantity as $index => $quantity) {
                $type = $request->bundle_discount_type[$index];
                $discountAmount = $request->bundle_discount_amount[$index];
                $price = $request->price;

                // Calculate after discount based on the type
                $afterDiscount = ($type === 'percentage')
                    ? max(0, ($quantity * $price) - (($discountAmount * ($quantity * $price)) / 100))
                    : max(0, ($quantity * $price) - $discountAmount);

                $bundleDetails[] = [
                    'quantity' => $quantity,
                    'type' => $type,
                    'discount_amount' => $discountAmount,
                    'product_price' => $price,
                    'after_discount' => $afterDiscount,
                ];
            }
        }

        $scheduleDetails = [];
        if ($request->has('is_subscribable')) {
            foreach ($request->schedule_interval as $index => $interval) {
                $scheduleDetails[] = [
                    'interval' => $interval,
                    'day' => $request->schedule_day[$index],
                    'time' => $request->schedule_time[$index],
                ];
            }
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->slug),
            'price' => $request->price,
            'quantity' => $request->quantity,

            // Bundle details
            'is_bundle' => $request->has('is_bundle'),
            'bundle_details' => $request->has('is_bundle') ? json_encode($bundleDetails) : null,

            // Subscription details
            'is_subscribable' => $request->has('is_subscribable'),
            'schedule_type' => $request->has('is_subscribable') ? $request->schedule_type : null,
            'schedule' => $request->has('is_subscribable') ? json_encode($scheduleDetails) : null,
        ]);

        return redirect()->back()->with('success', 'Product created successfully!');
    }


    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        $products = Product::latest()->paginate(4);
        return view('details', compact('product', 'products'));
    }


    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }

    //cart
    public function addCart(Request $request)
    {
        // return $request->all();
        $rules = [
            'product_id' => 'required',
            'purchase-type' => 'required',
        ];

        if ($request->input('purchase-type') == 'buy-now') {
            $rules['buy-now-quantity'] = 'required|integer|min:1';
        }
        if ($request->input('purchase-type') == 'schedule-buy') {
            $rules['schedule-quantity'] = 'required|integer|min:1';
            $rules['schedule-interval'] = 'required';
        }
        if ($request->input('purchase-type') == 'bulk') {
            $rules['bundle-quantity'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product = Product::findOrFail($request->product_id);

        $existProduct = ProductCart::where('product_id', $request->product_id)->first();

        if ($existProduct) {
            return redirect()->back()
                ->withErrors(['product' => 'The product is already in the cart.'])
                ->withInput();
        }
        if ($request->input('purchase-type') == 'schedule-buy') {

            $schedules = json_decode($product->schedule, true);
            $selectedInterval = $request->input('schedule-interval');


            $matchedSchedule = collect($schedules)->firstWhere('interval', $selectedInterval);


            $purchaseTypeDetails = $matchedSchedule ? json_encode($matchedSchedule) : null;
        } elseif ($request->input('purchase-type') == 'bulk') {

            $bundleDetails = json_decode($product->bundle_details, true);
            $selectedQuantity = $request->input('bundle-quantity');

            $matchedBundle = collect($bundleDetails)->firstWhere('quantity', $selectedQuantity);
            $purchaseTypeDetails = $matchedBundle ? json_encode($matchedBundle) : null;
        } else {
            $purchaseTypeDetails = null;
        }

        $productcart = ProductCart::create([
            'product_id' => $request->product_id,
            'product_price' => $product->price,
            'product_total_price' => $request->input('purchase-type') == 'bulk'
                ? $matchedBundle['after_discount']
                : ($request->input('purchase-type') == 'schedule-buy'
                    ? $request->input('schedule-quantity') * $product->price
                    : $request->input('buy-now-quantity') * $product->price),

            'product_quantity' => $request->input('purchase-type') == 'bulk'
                ? $selectedQuantity
                : ($request->input('purchase-type') == 'schedule-buy'
                    ? $request->input('schedule-quantity')
                    : $request->input('buy-now-quantity')),

            'purchase_type' => $request->input('purchase-type'),
            'schedule_type' => $request->input('purchase-type') == 'buy-now' ? null : $product->schedule_type,
            'purchase_type_details' => $purchaseTypeDetails,
        ]);
        return redirect()->back()->with('success', 'Product add cart successfully!');
    }

    public function cartShow()
    {
        $products = ProductCart::latest()->get();
        $total_cart_price = number_format($products->sum('product_total_price'), 2);
        return view('cart', compact('products', 'total_cart_price'));
    }

    public function cartUpdate(Request $request)
    {


        // Define validation rules
        $rules = [
            'id' => 'required|exists:product_carts,id', // Ensure the cart ID exists
            // 'schedule_type' => 'required|string|in:monthly,days', // Validate schedule_type as either 'monthly' or 'days'
            // 'product_quantity' => 'required|integer|min:1', // Ensure quantity is a positive integer
        ];
        if ($request->input('purchase_type') == 'buy-now') {
            $rules['buy-now-quantity'] = 'required';
        } elseif ($request->input('purchase_type') == 'schedule-buy') {
            $rules['schedule-buy-quantity_'] = 'required';
        } else {
            $rules['bulk-details'] = 'required';
        }
        // Define custom error messages (optional)
        $messages = [
            'id.required' => 'The cart ID is required.',
            'id.exists' => 'The specified cart item does not exist.',
            'buy-now-quantity.required' => 'The Quantity is required.',
            'schedule-buy-quantity_.required' => 'The Quantity is required.',
            'bulk-details.required' => 'The Quantity is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422); // Return 422 Unprocessable Entity for validation errors
        }
        // return $request->all();
        $cart = ProductCart::findOrFail($request->id);

        $cart->update([
            'purchase_type' => $request->input('purchase_type'),
        ]);
        if ($request->input('purchase_type') == 'buy-now') {
            $totalPrice = $request->input('buy-now-quantity') * $cart->product_price;
            $cart->update([
                'product_quantity' => $request->input('buy-now-quantity'),
                'product_total_price' => $totalPrice,
                'schedule_type' => null,
                'purchase_type_details' => null,
            ]);
        } elseif ($request->input('purchase_type') == 'schedule-buy') {
            $schedule = $cart->productDetails->schedule;
            $scheduleArray = json_decode($schedule, true);
            $val = $request->input('schedule-details');
            $filteredSchedules = array_filter($scheduleArray, function ($item) use ($val) {
                return isset($item['interval']) && $item['interval'] == $val; // Ensure the key exists before comparing
            });
            $filteredSchedules = json_encode(array_values($filteredSchedules));
            $totalPrice = $request->input('schedule-buy-quantity_') * $cart->product_price;
            $cart->update([
                'product_quantity' => $request->input('schedule-buy-quantity_'),
                'product_total_price' => $totalPrice,
                'schedule_type' => $request->input('schedule_type'),
                'purchase_type_details' => $filteredSchedules,
            ]);
        } else {
            $bundle = $cart->productDetails->bundle_details;
            $bundleArray = json_decode($bundle, true);
            $val = $request->input('bulk-details');
            $filteredBundle = array_filter($bundleArray, function ($item) use ($val) {
                return isset($item['quantity']) && $item['quantity'] == $val; // Ensure the key exists before comparing
            });
            $filteredBundles = json_encode(array_values($filteredBundle));
            $totalPrice = array_values($filteredBundle)[0]['after_discount'];
            $cart->update([
                'product_quantity' => $request->input('bulk-details'),
                'purchase_type_details' => $filteredBundles,
                'product_total_price' => $totalPrice,
                'schedule_type' => null,
            ]);
            $rules['bulk-details'] = 'required';
        }
        $total_cart_price = ProductCart::sum('product_total_price');
        return response()->json([
            'success' => true,
            'total_cart_price' => $total_cart_price,
            'message' => 'Cart Product Update successfully !'
        ]);
    }
    public function cartDelete($id)
    {
        $products = ProductCart::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Product delete form cart successfully!');
    }

    //order
    public function order(Request $request)
    {
        // return $request->all();
        $rules = [
            'product_id' => 'required',
            'purchase-type' => 'required',
        ];

        if ($request->input('purchase-type') == 'buy-now') {
            $rules['buy-now-quantity'] = 'required|integer|min:1';
        }
        if ($request->input('purchase-type') == 'schedule-buy') {
            $rules['schedule-quantity'] = 'required|integer|min:1';
            $rules['schedule-interval'] = 'required';
        }
        if ($request->input('purchase-type') == 'bulk') {
            $rules['bundle-quantity'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $product = Product::findOrFail($request->product_id);

        if ($request->input('purchase-type') == 'schedule-buy') {

            $schedules = json_decode($product->schedule, true);
            $selectedInterval = $request->input('schedule-interval');


            $matchedSchedule = collect($schedules)->firstWhere('interval', $selectedInterval);


            $purchaseTypeDetails = $matchedSchedule ? json_encode($matchedSchedule) : null;
        } elseif ($request->input('purchase-type') == 'bulk') {

            $bundleDetails = json_decode($product->bundle_details, true);
            $selectedQuantity = $request->input('bundle-quantity');

            $matchedBundle = collect($bundleDetails)->firstWhere('quantity', $selectedQuantity);

            $purchaseTypeDetails = $matchedBundle ? json_encode($matchedBundle) : null;
        } else {
            $purchaseTypeDetails = null;
        }

        $productorder = ProductOrder::create([
            'product_id' => $request->product_id,
            'product_price' => $product->price,
            'product_quantity' => $request->input('purchase-type') == 'bulk'
                ? $selectedQuantity
                : ($request->input('purchase-type') == 'schedule-buy'
                    ? $request->input('schedule-quantity')
                    : $request->input('buy-now-quantity')),

            'purchase_type' => $request->input('purchase-type'),
            'schedule_type' => $product->schedule_type,
            'purchase_type_details' => $purchaseTypeDetails,
        ]);
        return redirect()->back()->with('success', 'Product ordered successfully!');
    }

    //ajax
    public function productBundleDetails(Request $request)
    {
        try {
            $cart = ProductCart::findOrFail($request->id);
            $product = Product::findOrFail($cart->product_id);

            $bundleDetails = json_decode($product->bundle_details, true);

            $matchedBundle = collect($bundleDetails)->firstWhere('quantity', $request->quantity);

            if ($matchedBundle) {
                $quantity = $matchedBundle['quantity'];
                $afterDiscount = number_format($matchedBundle['after_discount'], 2);
                $productPrice = number_format($matchedBundle['product_price'], 2);
                return response()->json([
                    'success' => true,
                    'id' => $request->id,
                    'bundle' => $matchedBundle,
                    'afterDiscount' => $afterDiscount,
                    'productPrice' => $productPrice,
                ]);
            } else {
                // If no matching bundle is found
                return response()->json([
                    'success' => false,
                    'message' => 'No bundle found for the specified quantity.',
                ], 404);
            }
        } catch (ModelNotFoundException $e) {
            // If the product or cart is not found
            return response()->json([
                'success' => false,
                'message' => 'Product or Cart not found.',
            ], 404);
        } catch (Exception $e) {
            // Handle any other exceptions
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function cartBundleDetails(Request $request)
    {
        // Find the cart and decode the purchase type details
        $cart = ProductCart::findOrFail($request->id);

        // Check if bundle details exist
        if ($cart->purchase_type == 'bulk') {
            // Extract values
            $bundleDetails = json_decode($cart->purchase_type_details, true);
            // Check if $bundleDetails is not empty and is an array
            if (!empty($bundleDetails) && is_array($bundleDetails)) {
                // Iterate over each bundle detail
                foreach ($bundleDetails as $detail) {
                    // Access the fields for each bundle detail
                    $quantity = $detail['quantity'];  // Access quantity
                    $afterDiscount = number_format($detail['after_discount'], 2);  // Format after_discount
                    $productPrice = number_format($detail['product_price'], 2);  // Format product_price
                }
            }
            

            // Build HTML similar to the inputField format
            $inputField = "
                <span class=\"quantity-input quantity-display bulk-now-quantity-{$cart->id}\" 
                    data-unit-price=\"{$productPrice}\" 
                    data-id=\"{$cart->id}\" 
                    data-quantity=\"{$quantity}\">
                    {$quantity}
                </span>
            ";

            // Return the generated HTML and other relevant data
            return response()->json([
                'success' => true,
                'inputField' => $inputField,
                'totalPrice' => $afterDiscount,
                'quantity' => $quantity,
                'id' => $request->id,
                'find' => true,
            ]);
        } else {
            $productbundleDetails = json_decode($cart->productDetails->bundle_details, true);
            $quantity = $productbundleDetails[0]['quantity'];  // Adjust field names as per your structure
            $afterDiscount = number_format($productbundleDetails[0]['after_discount'], 2);  // Ensure it's formatted properly
            $productPrice = number_format($productbundleDetails[0]['product_price'], 2);

            $inputField = "
                <span class=\"quantity-input quantity-display bulk-now-quantity-{$cart->id}\" 
                    data-unit-price=\"{$productPrice}\" 
                    data-id=\"{$cart->id}\" 
                    data-quantity=\"{$quantity}\">
                    {$quantity}
                </span>
            ";
            return response()->json([
                'success' => true,
                'inputField' => $inputField,
                'totalPrice' => $afterDiscount,
                'quantity' => $quantity,
                'id' => $request->id,
                'find' => false,
            ]);
        }
    }
    public function cartScheduleDetails(Request $request)
    {
        $cart = ProductCart::findOrFail($request->id);
        $product_schedule_details = json_decode($cart->purchase_type_details, true);
        $inputField = "
                    <input type=\"number\" value=\"$cart->product_quantity\" 
                        name=\"schedule-buy-quantity\" 
                        id=\"schedule-buy-quantity-{$request->id}\" 
                        class=\"form-control quantity-input quantity-display\" 
                        data-unit-price=\"{$cart->product_price}\" 
                        data-id=\"{$request->id}\" 
                        data-quantity=\"{$cart->product_quantity}\" 
                        min=\"1\">
                ";
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'product_schedule_details' => $product_schedule_details,
            'inputField' => $inputField,
            'id' => $request->id,
        ]);
        return $request->all();
    }
}
