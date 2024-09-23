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
        // Step 1: Validation Rules
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'is_bundle' => 'nullable',
            'is_subscribable' => 'nullable',
        ];

        // Step 2: Add bundle-specific validation rules if is_bundle is true
        if ($request->input('is_bundle')) {
            $rules['bundle_quantity'] = 'required|array';
            $rules['bundle_quantity.*'] = 'required|integer|min:1';
            $rules['bundle_discount_type'] = 'required|array';
            $rules['bundle_discount_type.*'] = 'required|in:percentage,fixed';
            $rules['bundle_discount_amount'] = 'required|array';
            $rules['bundle_discount_amount.*'] = 'required|numeric|min:0';
        }

        // Step 3: Add subscription-specific validation rules if is_subscribable is true
        if ($request->input('is_subscribable')) {
            $rules['schedule_type'] = 'required|in:monthly,days';
            $rules['schedule_interval'] = 'required|array';
            $rules['schedule_interval.*'] = 'required|integer|min:1';
            $rules['schedule_day'] = 'required|array';
            $rules['schedule_day.*'] = 'required|integer|min:0|max:6';
            $rules['schedule_time'] = 'required|array';
            $rules['schedule_time.*'] = 'required|date_format:H:i'; // Ensure time format
        }

        // Step 4: Validate the request data
        $validator = Validator::make($request->all(), $rules);

        // Return with errors if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Step 5: Prepare bundle details if the product is a bundle
        // $bundleDetails = [];
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

        // Step 6: Prepare subscription schedule details if the product is subscribable
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
// return $request->has('is_bundle') ? json_encode($bundleDetails) : null;
        // Step 7: Create the product
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

        // Step 8: Redirect with success message
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
