<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class VehicleController extends Controller
{
    private function getToken(): string
    {
        $response = Http::baseUrl(config('services.glinche.url'))
            ->acceptJson()
            ->timeout(15)
            ->post('/api/partners/login', [
                'email'    => config('services.glinche.email'),
                'password' => config('services.glinche.password'),
            ])
            ->throw();

        return $response->json('token');
    }

    public function getVehicles()
    {
        $token = $this->getToken();

        $response = Http::baseUrl(config('services.glinche.url'))
            ->withToken($token)
            ->acceptJson()
            ->timeout(15)
            ->get('/api/partners/vehicles')
            ->throw();

        return $response->json();
    }
}
