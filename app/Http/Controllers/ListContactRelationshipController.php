<?php

namespace App\Http\Controllers;

use App\Models\ListContactRelationship;
use Illuminate\Http\Request;

class ListContactRelationshipController extends Controller
{
    // Get all relationships
    public function index()
    {
        return ListContactRelationship::with(['mailingList', 'emailContact'])->get();
    }

    // Store a new relationship
    public function store(Request $request)
    {
        $validated = $request->validate([
            'list_id' => 'required|exists:mailing_lists,id',
            'contact_id' => 'required|exists:email_contacts,id',
            'subscription_date' => 'required|date',
            'status' => 'required|in:subscribed,unsubscribed,pending',
        ]);

        $relationship = ListContactRelationship::create($validated);
        return response()->json($relationship, 201);
    }

    // Show a single relationship
    public function show($id)
    {
        $relationship = ListContactRelationship::with(['mailingList', 'emailContact'])->findOrFail($id);
        return response()->json($relationship);
    }

    // Update a relationship
    public function update(Request $request, $id)
    {
        $relationship = ListContactRelationship::findOrFail($id);

        $validated = $request->validate([
            'list_id' => 'sometimes|exists:mailing_lists,id',
            'contact_id' => 'sometimes|exists:email_contacts,id',
            'subscription_date' => 'sometimes|date',
            'status' => 'sometimes|in:subscribed,unsubscribed,pending',
        ]);

        $relationship->update($validated);
        return response()->json($relationship);
    }

    // Delete a relationship
    public function destroy($id)
    {
        ListContactRelationship::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
