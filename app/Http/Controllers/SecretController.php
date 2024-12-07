<?php

namespace App\Http\Controllers;

use App\Models\Secret;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SecretController extends Controller
{
    public function create()
    {
        return view('home');
    }

    public function success(Secret $secret){
        $urlKey = session('urlKey');
        $shareLink = url("secret/{$secret->url_identifier}?key={$urlKey}");

        return view('secrets.success', ['shareLink' => $shareLink, 'secret' => $secret]);
    }

    public function show(Request $request, $url_identifier)
    {


        // Buscar el secreto en la base de datos usando el url_identifier
        $secret = Secret::where('url_identifier', $url_identifier)->firstOrFail();
        if(($secret->clicks_expiration && $secret->clicks_remaining <= 0) || ($secret->views_expiration && $secret->views_remaining <= 0)){
            abort(404);
        }

        return view('secrets.show', ['secret' => $secret]);
    }

    /**
     * Actualiza el contador de clicks y vistas restantes del secreto
     */


    public function delete(Secret $secret){
        $secret->delete();
        return redirect()->route('home');
    }


}
