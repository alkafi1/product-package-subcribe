<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:products,slug',
        'price' => 'required|numeric|min:0',
        'quantity' => 'required|integer|min:0',

        // Bundle validation
        'is_bundle' => 'nullable|in:on',
        'bundle_quantity' => 'required_if:is_bundle,on|array',
        'bundle_quantity' => 'integer|min:1',
        'bundle_discount_type'=> 'required_if:is_bundle,on|array',
        'bundle_discount_type' => 'in:percentage,fixed',
        'bundle_discount_amount' => 'required_if:is_bundle,on|array',
        'bundle_discount_amount' => 'numeric|min:0',

        // Subscription validation
        'is_subscribable' => 'nullable|in:on',
        'schedule_type' => 'required_if:is_subscribable,on|in:monthly,days',
        'schedule_interval' => 'required_if:is_subscribable,on|integer|min:1',
        'schedule_day' => 'required_if:is_subscribable,on|integer|min:0|max:6', // Assuming day of the week (0 = Sunday)
        'schedule_time' => 'required_if:is_subscribable,on',
    ]);

    // If validation fails, return errors
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Step 1: Generate bundle details
    $bundleDetails = [];
    if ($request->has('is_bundle')) {
        foreach ($request->bundle_quantity as $index => $quantity) {
            $type = $request->bundle_discount_type[$index];
            $discount_amount = $request->bundle_discount_amount[$index];
            $price = $request->price;

            // Calculate after discount based on the type
            $after_discount = ($type === 'percentage')
                ? max(0, ($quantity * $price) - (($discount_amount * ($quantity * $price)) / 100))
                : max(0, ($quantity * $price) - $discount_amount);

            $bundleDetails[] = [
                'quantity' => $quantity,
                'type' => $type,
                'discount_amount' => $discount_amount,
                'product_price' => $price,
                'after_discount' => $after_discount,
            ];
        }
    }

    // Step 2: Generate subscription schedule details
    $schedule = [];
    if ($request->has('is_subscribable')) {
        foreach ($request->schedule_interval as $index => $interval) {
            $schedule[] = [
                'interval' => $interval,
                'day' => $request->schedule_day[$index],
                'time' => $request->schedule_time[$index],
            ];
        }
    }

    // Step 3: Create the product and save the details
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
        'schedule' => $request->has('is_subscribable') ? json_encode($schedule) : null,
    ]);

    // Step 4: Redirect to a success page or return success response
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
}
