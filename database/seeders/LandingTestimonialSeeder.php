<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LandingTestimonial;

class LandingTestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testimonials = [
            [
                'client_name' => 'Rebekah Bower',
                'client_role' => 'Wisconsin',
                'testimonial' => 'Patti is very professional and thorough. She spent the whole day at the house for our first initial deep clean. Loved seeing all the ways to fold towels and Kleenex. Our woodwork and blinds look beautiful and dust free. Highly recommend!',
                'rating' => 5,
                'order' => 1,
            ],
            [
                'client_name' => 'Maria McClellan',
                'client_role' => 'Wisconsin',
                'testimonial' => 'Patty and co-worker did a great deep clean of my home! They were very professional and returned calls and texts immediately.',
                'rating' => 5,
                'order' => 2,
            ],
            [
                'client_name' => 'Redwood Retreat',
                'client_role' => 'Wisconsin',
                'testimonial' => 'After being laid up after a surgery I call Patti to ask about her service, she came out that day, gave me a quote. I had my house cleaned in a few days to absolute perfection. 100% recommend this cleaning service.',
                'rating' => 5,
                'order' => 3,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            LandingTestimonial::create($testimonial);
        }
    }
}
