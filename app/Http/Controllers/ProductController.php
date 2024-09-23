<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $products = Product::latest()->paginate(6); // Adjust the number per page as needed
        return view('shop', compact('products'));
    }
    public function home()
    {

        $products = Product::latest()->paginate(6); // Adjust the number per page as needed
        return view('shop', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('details', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

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

        // Return with errors if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $product = Product::findOrFail($request->product_id);

        if ($request->input('purchase-type') == 'schedule-buy') {
            // Retrieve and decode the product's schedule
            $schedules = json_decode($product->schedule, true);
            $selectedInterval = $request->input('schedule-interval');

            // Find the matching schedule
            $matchedSchedule = collect($schedules)->firstWhere('interval', $selectedInterval);

            // Prepare the purchase_type_details for schedule-buy
            $purchaseTypeDetails = $matchedSchedule ? json_encode($matchedSchedule) : null;
        } elseif ($request->input('purchase-type') == 'bulk') {
            // Decode the product's bundle_details JSON
            $bundleDetails = json_decode($product->bundle_details, true);
            $selectedQuantity = $request->input('bundle-quantity');

            // Find the matching bundle detail
            $matchedBundle = collect($bundleDetails)->firstWhere('quantity', $selectedQuantity);

            // Prepare the purchase_type_details for bulk
            $purchaseTypeDetails = $matchedBundle ? json_encode($matchedBundle) : null;
        } else {
            $purchaseTypeDetails = null; // Default case
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
            'purchase_type_details' => $purchaseTypeDetails,
        ]);
        return redirect()->back()->with('success', 'Product ordered successfully!');
    }
}
