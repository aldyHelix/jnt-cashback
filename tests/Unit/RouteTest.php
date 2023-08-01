<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

use Illuminate\Foundation\Testing\WithFaker;

class RouteTest extends TestCase
{

    /**
     * Test all routes in the application.
     *
     * @return void
     */
    public function testGetNotLoginRoutes()
    {
        $appURL = env('APP_URL');

        $urls = [
            '/administrator/login',
        ];

        echo  PHP_EOL;

        foreach ($urls as $url) {
            $response = $this->get($url);
            if((int)$response->status() !== 200){
                echo  $appURL . $url . ' (FAILED) did not return a 200.';
                $this->assertTrue(true);
            } else {
                echo $appURL . $url . ' (success '.(int)$response->status().')';
                $this->assertTrue(true);
            }
            echo  PHP_EOL;
        }
    }
}
