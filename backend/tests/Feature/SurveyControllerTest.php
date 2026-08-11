<?php

namespace Tests\Feature;

use Tests\TestCase;

class SurveyControllerTest extends TestCase
{
    public function test_list_endpoint_returns_all_codes(): void
    {
        $response = $this->getJson('/api/list.json');

        $response->assertOk();
        $response->assertJsonFragment(['code' => 'XX1', 'name' => 'Paris']);
        $response->assertJsonFragment(['code' => 'XX2', 'name' => 'Chartres']);
        $response->assertJsonFragment(['code' => 'XX3', 'name' => 'Melun']);
    }

    public function test_show_endpoint_returns_aggregated_results_for_a_known_code(): void
    {
        $response = $this->getJson('/api/XX1.json');

        $response->assertOk();
        $response->assertJson([
            [
                'type' => 'qcm',
                'label' => 'What best sellers are available in your store?',
                'result' => [
                    'Product 1' => 0,
                    'Product 2' => 2,
                    'Product 3' => 1,
                    'Product 4' => 0,
                    'Product 5' => 4,
                    'Product 6' => 0,
                ],
            ],
            [
                'type' => 'numeric',
                'label' => 'Number of products?',
                'result' => 697.2,
            ],
        ]);
    }

    public function test_show_endpoint_returns_404_for_unknown_code(): void
    {
        $response = $this->getJson('/api/UNKNOWN.json');

        $response->assertNotFound();
    }
}
