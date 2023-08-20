<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{

    public function authorize(): bool
    {
        return $this->user() != null;
    }


    public function rules(): array
    {
        return [
            'city'    => ['nullable'],
            'country' => ['required'],
        ];
    }
}
