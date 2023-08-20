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
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function show(int $id)
    {
        $address = Address::where('contact_id', $id)->first();
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


        if (!$address) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "address not yet created"
                    ]
                ]
            ])->setStatusCode(404));
        }


        return AddressResource::make($address);
    }

    public function update(AddressRequest $request, string $id): AddressResource
    {
        $validated = $request->validated();
        $contact = Contact::query()->where('id', $id)->first();
        $address = Address::query()->where('contact_id', $id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "contact not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        if (!$address) {
            $address = new Address($validated);
            $address->contact_id = $id;
            $address->save();
            return new AddressResource($address);
        }

        $address->update($validated);

        return new AddressResource($address);
    }

    public function destroy(string $id)
    {
        //
    }
}
