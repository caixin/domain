<?php

namespace App\Http\Requests\Domain;

use App\Http\Requests\FormRequest;

final class NodeForm extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => 'required',
            'server_ip' => 'required',
        ];
    }
}
