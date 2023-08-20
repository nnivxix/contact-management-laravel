<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\AddressResource;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(AddressRequest $request, int $id)
    {
        $validated = $request->validated();
        $contact = Contact::where('id', $id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "contact not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address = new Address($validated);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
