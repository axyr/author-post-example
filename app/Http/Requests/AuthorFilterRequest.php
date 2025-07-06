<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthorFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // names is filter as per the requirement,
            // but we could also name this gender...
            'filter' => [
                'nullable',
                Rule::enum(Gender::class),
            ],
            // this doesn't scale very well, but works for this demo.
           'sort' => 'nullable|in:first_name:asc,rating:asc,post_title:asc,first_name:desc,rating:desc,post_title:desc',
        ];
    }
}
