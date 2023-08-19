<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $contacts = Contact::query()
            ->where('user_id', $user->id)
            ->when(
                $request->has('name'),
                function ($query) use ($request) {
                    $query->where('first_name', 'like', '%' .  $request->input('name') . '%')
                        ->orWhere('last_name', 'like', '%' .  $request->input('name') . '%');
                }
            )
            ->when(
                $request->has('email'),
                function ($query) use ($request) {
                    $query->where('email', 'like', '%' .  $request->input('email') . '%');
                }
            )
            ->when(
                $request->has('phone'),
                function ($query) use ($request) {
                    $query->where('phone', 'like', '%' .  $request->input('phone') . '%');
                }
            )
            ->paginate(perPage: $size, page: $page);

        return ContactResource::collection($contacts);
    }

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
                        "contact not found"
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
                        "contact not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $contact->fill($validated);
        $contact->save();

        return new ContactResource($contact);
    }


    public function destroy(string $id): JsonResponse
    {
        $contact = Contact::query()->whereId($id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "contact not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $contact->delete();
        return response()
            ->json([
                'message' => "Contact remove successfuly"
            ])
            ->setStatusCode(200);
    }
}
