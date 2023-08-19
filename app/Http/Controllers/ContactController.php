<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ContactResource;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactController extends Controller
{

    public function store(ContactRequest $request): JsonResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        $contact = new Contact($validated);
        $contact['user_id'] = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function show(string $id)
    {
        $contact = Contact::query()->whereId($id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new ContactResource($contact);
    }

    public function update(ContactRequest $request, string $id)
    {
        $contact = Contact::query()->whereId($id)->first();
        $validated = $request->validated();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $contact->fill($validated);
        $contact->save();

        return new ContactResource($contact);
    }


    public function destroy(string $id)
    {
        //
    }
}
