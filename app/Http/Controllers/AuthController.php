<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\Transformers\UserTransformer;
use Auth;

class AuthController extends Controller
{
    public function register(Request $request, User $user)
    {
    	$this->validate($request, [
    		'name'            => 'required|max:255',
            'email'           => 'required|email|max:255|unique:users',
            'password'        => 'required|min:6',
            'no_hp'           => 'required',
            'alamat'          => 'required',
            'kota_domisili'   => 'required',
            'negara_domisili' => 'required',
        ]);

        $user = $user->create([
        	'name'            => $request->name,
            'email'           => $request->email,
            'password'        => bcrypt($request->password),
            'no_hp'           => $request->no_hp,
            'alamat'          => $request->alamat,
            'kota_domisili'   => $request->kota_domisili,
            'negara_domisili' => $request->negara_domisili,
            'status_akun'     => "Pelanggan",
            'api_token'       => bcrypt($request->email),
        ]);

        $response = fractal()
            ->item($user)
            ->transformWith(new UserTransformer)
            ->addMeta([
            	'token' => $user->api_token,
            	])
            ->toArray();

        return response()->json($response, 201);
    }

    public function login(Request $request, User $user)
    {
    	if(!Auth::attempt([
    		'email' => $request->email, 
    		'password' => $request->password])){
    		return response()->json(['error' => 'Username atau Password salah'], 401);
    	}

    	$user = $user->find(Auth::user()->id);

    	return fractal()
            ->item($user)
            ->transformWith(new UserTransformer)
            ->addMeta([
            	'token' => $user->api_token,
            	])
            ->toArray();
    }
}
