<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChartOfAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'type_code' => [
                'required',
                'string',
                'size:3',
                Rule::exists('account_types', 'code'),
            ],
            'group_code' => [
                'required',
                'string',
                'size:8',
                Rule::exists('account_groups', 'code')->where(function ($query) {
                    return $query->where('type_code', $this->type_code);
                }),
            ],
            'class_code' => [
                'required',
                'string',
                'size:2',
                Rule::exists('account_classes', 'code')->where(function ($query) {
                    return $query->where('type_code', $this->type_code)
                        ->where('group_code', $this->group_code);
                }),
            ],
            'account_code' => [
                'required',
                'string',
                'size:19',
                'regex:/^2184\d{15}$/',
                Rule::unique('chart_of_accounts')->ignore($this->chart_of_account),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'boolean',
            ],
            'parent_id' => [
                'nullable',
                'exists:chart_of_accounts,id',
            ],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'type_code.required' => 'The account type is required.',
            'type_code.exists' => 'The selected account type is invalid.',
            'group_code.required' => 'The account group is required.',
            'group_code.exists' => 'The selected account group is invalid for the chosen type.',
            'class_code.required' => 'The account class is required.',
            'class_code.exists' => 'The selected account class is invalid for the chosen group.',
            'account_code.required' => 'The account code is required.',
            'account_code.unique' => 'This account code is already in use for the selected type, group, and class.',
            'name.required' => 'The account name is required.',
            'parent_id.exists' => 'The selected parent account is invalid.',
        ];
    }
} 