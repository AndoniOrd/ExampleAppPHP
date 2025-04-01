<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmailContactsImport;


class EmailContactFileController extends Controller
{
    /**
     * Display the form for uploading the Excel file.
     */
    public function showImportForm()
    {
        return view('emailContacts.import');
    }

    /**
     * Handle the import of the Excel file.
     */
    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|file|mimes:xlsx'
        ]);

        // Import the Excel file
        Excel::import(new EmailContactsImport, $request->file('file'));

        return redirect()->back()->with('status', 'Import Successful');
    }
}
