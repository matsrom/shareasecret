<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiProxyController extends Controller
{
    public function getIpLocation(Request $request)
    {
        $ip = $request->ip(); // Obtener la IP del cliente

        $response = Http::withOptions(['verify' => false])->get('https://api.ip2location.io/', [
            'key' => '78738552C7CE2DF260F21C9EE99E9099',
            'ip' => $ip
        ]);

        return response()->json($response->json());
    }
}
