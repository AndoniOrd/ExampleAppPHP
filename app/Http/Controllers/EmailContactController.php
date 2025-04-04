<?php

namespace App\Http\Controllers;

use App\Models\EmailContact;
use Illuminate\Http\Request;

class EmailContactController extends Controller
{
    public function index()
    {
        return EmailContact::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:email_contacts', // Changed table name
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,pending',
            'source' => 'required|in:web,api,manual',
            'opt_in_date' => 'required|date',
            'opt_in_confirmation' => 'required|boolean',
            'custom_fields' => 'nullable|json',
            'creation_date' => 'required|date',
            'last_updated_date' => 'required|date',
        ]);

        return EmailContact::create($validated);
    }

    public function show(EmailContact $emailContact) // Fixed parameter name
    {
        return $emailContact;
    }

    public function update(Request $request, EmailContact $emailContact)
    {
        $validated = $request->validate([
            'email' => 'email|unique:email_contacts,email,'.$emailContact->id, // Changed table name
            'name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'status' => 'in:active,inactive,pending',
            'source' => 'in:web,api,manual',
            'opt_in_date' => 'date',
            'opt_in_confirmation' => 'boolean',
            'custom_fields' => 'nullable|json',
            'creation_date' => 'date',
            'last_updated_date' => 'date',
        ]);

        $emailContact->update($validated);

        return $emailContact;
    }

    public function destroy(EmailContact $emailContact)
    {
        $emailContact->delete();

        return response()->noContent();
    }
}