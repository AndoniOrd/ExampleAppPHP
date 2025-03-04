<?php
namespace App\Http\Controllers;

use App\Models\MailingList;
use Illuminate\Http\Request;

class MailingListController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'creation_date'   => 'required|date',
            'last_updated_date'=> 'nullable|date',
            'owner_id'        => 'required|integer|exists:users,id',
            'status'          => 'required|string|in:draft,active,archived',
            'type'            => 'required|string|in:newsletter,promotions,updates',
            'tags'            => 'nullable|string',
        ];

        $data = $request->validate($rules);

        $mailingList = MailingList::create($data);

        return response()->json([
            'data' => [
                'id' => $mailingList->id,
                'name' => $mailingList->name,
                'status' => $mailingList->status,
                'type' => $mailingList->type
            ]
        ], 201);
    }

    public function show(MailingList $mailingList)
    {
        return response()->json([
            'data' => [
                'id' => $mailingList->id,
                'name' => $mailingList->name,
                'type' => $mailingList->type
            ]
        ]);
    }

    public function update(Request $request, MailingList $mailingList)
    {
        $rules = [
            'name'            => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'creation_date'   => 'nullable|date',
            'last_updated_date'=> 'nullable|date',
            'owner_id'        => 'nullable|integer|exists:users,id',
            'status'          => 'nullable|string|in:draft,active,archived',
            'type'            => 'nullable|string|in:newsletter,promotions,updates',
            'tags'            => 'nullable|string',
        ];

        $data = $request->validate($rules);

        $mailingList->update($data);

        return response()->json([
            'data' => [
                'id' => $mailingList->id,
                'name' => $mailingList->name,
                'type' => $mailingList->type
            ]
        ]);
    }
}