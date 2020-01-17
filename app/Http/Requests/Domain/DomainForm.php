<?php

namespace App\Http\Requests\Domain;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

final class DomainForm extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'domain'   => ['required',Rule::unique('domain')->ignore($this->route('domain'))],
            'group_id' => 'required|int',
        ];
    }
}
