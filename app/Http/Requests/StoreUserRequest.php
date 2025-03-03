<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Allow all users; update this as needed.
        return true;
    }
    
    public function rules(): array
    {
        return [
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email_address'  => 'required|email|unique:users,email_address',
            'password'       => 'required|string|min:6',
            'phone_number'   => 'nullable|string|max:20',
            'role'           => 'required|in:admin,editor,viewer',
            'account_status' => 'required|in:active,inactive,suspended',
            // Validate that creation_date is provided and is a valid date
            'creation_date'  => 'required|date',
            // last_login can be nullable but must be a valid datetime if provided
            'last_login'     => 'nullable|date',
            'company_name'   => 'nullable|string|max:255',
            'company_address'=> 'nullable|string|max:255',
            'vat_tax_id'     => 'nullable|string|max:50',
            'industry'       => 'nullable|string|max:255',
            'company_size'   => 'nullable|integer',
            'website'        => 'nullable|url',
        ];
    }
}
