<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{

    public function store(ContactCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        $contact = new Contact($validated);
        $contact['user_id'] = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
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
