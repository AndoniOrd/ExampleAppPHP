<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplates;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailTemplatesController extends Controller
{
    /**
     * Display a listing of the email templates.
     */
    public function index()
    {
        $templates = EmailTemplates::all();
        return response()->json($templates);
    }

    /**
     * Store a newly created email template in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string',
            'subject_line'        => 'required|string|max:255',
            'html_content'        => 'required|string',
            'plain_text_version'  => 'required|string',
            'creator'             => 'required|exists:users,id',
            'creation_date'       => 'required|date',
            'last_updated_date'   => 'required|date',
            'category'            => 'required|string|max:255',
            // The enum in your migration accepts: draft, active, archived.
            'status'              => 'required|in:draft,active,archived',
            'preview_image_url'   => 'nullable|url',
        ]);

        $template = EmailTemplates::create($validatedData);
        return response()->json($template, Response::HTTP_CREATED);
    }

    /**
     * Display the specified email template.
     */
    public function show(EmailTemplates $emailTemplate)
    {
        return response()->json($emailTemplate);
    }

    public function upload(Request $request)
{
    $request->validate([
        'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($request->hasFile('upload')) {
        $originName = $request->file('upload')->getClientOriginalName();
        $fileName = pathinfo($originName, PATHINFO_FILENAME);
        $extension = $request->file('upload')->getClientOriginalExtension();
        $fileName = $fileName . '_' . time() . '.' . $extension;

        $request->file('upload')->storeAs('public/uploads/email-templates', $fileName);

        $url = asset('storage/uploads/email-templates/' . $fileName);

        return response()->json([
            'fileName' => $fileName,
            'uploaded' => 1,
            'url' => $url
        ]);
    }

    return response()->json([
        'uploaded' => 0,
        'error' => ['message' => 'File upload failed']
    ], 400);
}

    /**
     * Update the specified email template in storage.
     */
    public function update(Request $request, EmailTemplates $emailTemplate)
    {
        $validatedData = $request->validate([
            'name'                => 'sometimes|required|string|max:255',
            'description'         => 'sometimes|nullable|string',
            'subject_line'        => 'sometimes|required|string|max:255',
            'html_content'        => 'sometimes|required|string',
            'plain_text_version'  => 'sometimes|required|string',
            'creator'             => 'sometimes|required|exists:users,id',
            'creation_date'       => 'sometimes|required|date',
            'last_updated_date'   => 'sometimes|required|date',
            'category'            => 'sometimes|required|string|max:255',
            'status'              => 'sometimes|required|in:draft,active,archived',
            'preview_image_url'   => 'sometimes|nullable|url',
        ]);

        $emailTemplate->update($validatedData);
        return response()->json($emailTemplate);
    }

    /**
     * Remove the specified email template from storage.
     */
    public function destroy(EmailTemplates $emailTemplate)
    {
        $emailTemplate->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
