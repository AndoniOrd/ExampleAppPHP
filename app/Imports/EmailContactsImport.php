<?php

namespace App\Imports;

use App\Models\EmailContact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmailContactsImport implements ToModel, WithHeadingRow
{
    /**
     * Map each row of the Excel file to a new EmailContact.
     *
     * Expected Excel headers:
     * email, name, last_name, status, source, 
     * opt_in_date, opt_in_confirmation, custom_fields, creation_date, last_updated_date
     */
    public function model(array $row)
    {
        return new EmailContact([
            'email'       => $row['email'] ?? null,
            'name'          => $row['name'] ?? null,
            'last_name'           => $row['last_name'] ?? null,
            'status'              => $row['status'] ?? 'active',
            'source'              => $row['source'] ?? null,
            'opt_in_date'         => isset($row['opt_in_date']) ? \Carbon\Carbon::parse($row['opt_in_date']) : null,
            'opt_in_confirmation' => $row['opt_in_confirmation'] ?? false,
            'custom_fields'       => isset($row['custom_fields']) ? json_decode($row['custom_fields'], true) : [],
            'creation_date'       => isset($row['creation_date']) ? \Carbon\Carbon::parse($row['creation_date']) : now(),
            'last_updated_date'   => isset($row['last_updated_date']) ? \Carbon\Carbon::parse($row['last_updated_date']) : now(),
        ]);
    }
}
