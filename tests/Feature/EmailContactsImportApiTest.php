<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmailContactsImport;
use PHPUnit\Framework\Attributes\Test;



class EmailContactsImportApiTest extends TestCase
{
     #[Test]
    public function testEmailContactsImportApiEndpoint()
    {
        Excel::shouldReceive('import')
            ->once()
            ->with(\Mockery::type(EmailContactsImport::class), \Mockery::any())
            ->andReturnNull();

        // Create a fake Excel file with .xlsx extension.
        $file = UploadedFile::fake()->create('contacts.xlsx');

        // Act: Make a POST request to the API endpoint.
        $response = $this->postJson('/api/import-email-contacts', [
            'file' => $file,
        ]);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Import Successful']);
    }
}
