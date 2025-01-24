<?php

namespace App\Http\Controllers\Backend;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;

class CustomersController extends Controller
{
    public function post (ClientRequest $request) {

        $client = new Client();
        $client->first_name = $request->get('first_name');
        $client->last_name = $request->get('last_name');
        $client->phone = $request->get('phone');
        $client->company_name = $request->get('company_name');
        $client->address = $request->get('address');
        $client->email = $request->get('email');
        $client->account_no = $request->get('account_no');
        $client->client_type = $request->client_type;
        $client->save();

    	return response()->json(['created'], 201);
    }
}
