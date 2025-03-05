<?php

namespace App\Http\Controllers;

use App\Models\MailingList;
use Illuminate\Http\Request;

class MailingListController extends Controller
{
    /**
     * Listar todos los mailing lists.
     */
    public function index()
    {
        $mailingLists = MailingList::all();
        return response()->json(['data' => $mailingLists]);
    }

    /**
     * Crear un mailing list.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'creation_date' => 'required|date',
            'last_updated_date' => 'nullable|date',
            'owner_id' => 'required|integer|exists:users,id',
            'status' => 'required|string|in:draft,active,archived',
            'type' => 'required|string|in:newsletter,promotions,updates',
            'tags' => 'nullable|string',
        ];

        $data = $request->validate($rules);

        $mailingList = MailingList::create($data);

        return response()->json([
            'data' => [
                'id' => $mailingList->id,
                'name' => $mailingList->name,
                'status' => $mailingList->status,
                'type' => $mailingList->type,
            ]
        ], 201);
    }

    /**
     * Mostrar un mailing list.
     */
    public function show(int $id)
    {
       
        $mailingList = MailingList::findOrFail( $id );

        return response()->json([
            'data' => [
                'id' => $mailingList->id,
                'name' => $mailingList->name,
                'type' => $mailingList->type,
            ]
        ]);
    }

    /**
     * Actualizar un mailing list.
     */
    public function update(Request $request, int $id)
    {
        $rules = [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'creation_date' => 'nullable|date',
            'last_updated_date' => 'nullable|date',
            'owner_id' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|string|in:draft,active,archived',
            'type' => 'nullable|string|in:newsletter,promotions,updates',
            'tags' => 'nullable|string',
        ];

        $mailingList = MailingList::findOrFail( $id );

        $data = $request->validate($rules);

        $mailingList->update($data);
        $mailingList->refresh(); // actualiza el modelo con los datos nuevos

        return response()->json([
            'data' => [
                'id' => $mailingList->id,
                'name' => $mailingList->name,
                'type' => $mailingList->type,
            ]
        ]);
    }

    public function destroy(int $id)
    {
        $mailingList = MailingList::findOrFail( $id );

        $mailingList->delete();
        return response()->noContent();
    }
}