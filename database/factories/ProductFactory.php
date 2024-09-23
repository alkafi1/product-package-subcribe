<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{

    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        $slug = Str::slug($name);
        $price = $this->faker->randomFloat(2, 10, 500); // Price between 10 and 500
        $quantity = $this->faker->numberBetween(1, 100);

        // Random boolean for bundle and subscribable products
        $isBundle = $this->faker->boolean(70); // 30% chance of being a bundle
        $isSubscribable = $this->faker->boolean(70); // 20% chance of being subscribable

        // Generate bundle details if the product is a bundle
        $bundleDetails = [];
        if ($isBundle) {
            // Generate multiple bundle options, each with a unique quantity and discount
            $bundleQuantities = $this->faker->randomElements([3, 5, 7, 10], $this->faker->numberBetween(1, 3)); // 1-3 bundles

            foreach ($bundleQuantities as $bundleQuantity) {
                $type = $this->faker->randomElement(['percentage', 'fixed']);

                // Calculate amount based on the type of discount
                $discount_amount = ($type === 'percentage')
                    ? $this->faker->numberBetween(10, 50)  // 1% to 50% discount
                    : $this->faker->numberBetween(100, 150); // Fixed amount discount between 100 and 150

                    $after_discount = max(0, ($type === 'percentage')
                    ? ($bundleQuantity * $price) - (($discount_amount * ($bundleQuantity * $price)) / 100)
                    : ($bundleQuantity * $price) - $discount_amount);
                $bundleDetails[] = [
                    'quantity' => $bundleQuantity,
                    'type' => $type,
                    'discount_amount' => $discount_amount,
                    'product_price' => $price,
                    'after_discount' => $after_discount,
                ];
            }
        }

        // Generate subscription details if the product is subscribable
        $schedule = null;
        $scheduleType = null;
        if ($isSubscribable) {
            // Randomly choose the schedule type: 'monthly' or 'days'
            $scheduleType = $this->faker->randomElement(['monthly', 'days']);

            if ($scheduleType === 'monthly') {
                $schedule = [
                    [
                        'interval' => 1,
                        'day' => 1,
                        'time' => '10:00 AM',
                    ],
                    [
                        'interval' => 2,
                        'day' => 1,
                        'time' => '10:00 AM',
                    ]
                ];
            } else {
                $schedule = [
                    [
                        'interval' => 7,
                        'day' => 0, // Day of the week (0 = Sunday)
                        'time' => '09:00 AM',
                    ],
                    [
                        'interval' => 14,
                        'day' => 0, // Day of the week (0 = Sunday)
                        'time' => '09:00 AM',
                    ]
                ];
            }
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'price' => $price,
            'quantity' => $quantity,
            'is_bundle' => $isBundle,
            'bundle_details' => $bundleDetails ? json_encode($bundleDetails) : null,
            'is_subscribable' => $isSubscribable,
            'schedule_type' => $scheduleType,
            'schedule' => $schedule ? json_encode($schedule) : null,
        ];
    }
}
