<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmailContactsImport;

class EmailContactApiController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx'
        ]);

        try {
            Excel::import(new EmailContactsImport, $request->file('file'));

            return response()->json(['message' => 'Import Successful'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error during import: ' . $e->getMessage()], 500);
        }
    }
}
