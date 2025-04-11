<?php

namespace App\Imports;

use App\Models\EmailContact;
use App\Models\MailingList;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EmailContactsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading
{
    use SkipsFailures;

    protected $mailingListIds;
    protected $existingEmails = [];
    protected $invalidEmails = [];
    protected $emptyData = [];
    protected $successCount = 0;
    protected $currentRow = 2;

    public function __construct(array $mailingListIds = [])
    {
        $this->mailingListIds = $mailingListIds;
    }

    public function model(array $row)
    {
        // Debug: Log the row data to check structure
        Log::debug('Import row data:', $row);

        $this->currentRow++;

        // Skip empty rows
        if (empty(array_filter($row))) {
            $this->emptyData[] = [
                'row' => $this->currentRow - 1,
                'email' => $row['email'] ?? 'empty',
                'reason' => 'Empty row'
            ];
            return null;
        }

        // Check required fields
        if (empty($row['email']) || empty($row['name'])) {
            $this->emptyData[] = [
                'row' => $this->currentRow - 1,
                'email' => $row['email'] ?? 'empty',
                'reason' => 'Missing required fields - Email: ' . ($row['email'] ?? 'missing') . ', Name: ' . ($row['name'] ?? 'missing')
            ];
            return null;
        }

        // Validate email format
        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            $this->invalidEmails[] = [
                'row' => $this->currentRow - 1,
                'email' => $row['email'],
                'reason' => 'Invalid email format'
            ];
            return null;
        }

        // Check for existing email
        $existingContact = EmailContact::where('email', $row['email'])->first();
        if ($existingContact) {
            $this->existingEmails[] = [
                'row' => $this->currentRow - 1,
                'email' => $row['email'],
                'reason' => 'Email already exists'
            ];

            // Still add to mailing lists if needed, even though it's an existing contact
            if (!empty($this->mailingListIds)) {
                $existingContact->mailingLists()->syncWithoutDetaching($this->mailingListIds);
            }
            
            return null;
        }

        // Handle dates with better error handling
        try {
            $creationDate = !empty($row['creation_date']) 
                ? Carbon::parse($row['creation_date']) 
                : now();
            
            $lastUpdatedDate = !empty($row['last_updated_date']) 
                ? Carbon::parse($row['last_updated_date']) 
                : now();
                
            $optInDate = !empty($row['opt_in_date']) 
                ? Carbon::parse($row['opt_in_date']) 
                : now();
        } catch (\Exception $e) {
            Log::error('Date parsing error: ' . $e->getMessage(), $row);
            $this->invalidEmails[] = [
                'row' => $this->currentRow - 1,
                'email' => $row['email'],
                'reason' => 'Invalid date format: ' . $e->getMessage()
            ];
            return null;
        }

        // Parse opt_in_confirmation more robustly
        $optInConfirmation = false;
        if (isset($row['opt_in_confirmation'])) {
            $value = $row['opt_in_confirmation'];
            if (is_bool($value)) {
                $optInConfirmation = $value;
            } elseif (is_string($value)) {
                $value = strtolower(trim($value));
                $optInConfirmation = in_array($value, ['true', 'yes', '1', 'y', 'si', 'sí']);
            } elseif (is_numeric($value)) {
                $optInConfirmation = (bool)$value;
            }
        }

        // Create new contact with better error handling
        try {
            $contact = new EmailContact([
                'email' => trim($row['email']),
                'name' => trim($row['name']),
                'last_name' => trim($row['last_name'] ?? ''),
                'status' => $row['status'] ?? 'active',
                'source' => $row['source'] ?? 'import',
                'opt_in_date' => $optInDate,
                'opt_in_confirmation' => $optInConfirmation,
                'custom_fields' => $this->parseCustomFields($row['custom_fields'] ?? null),
                'creation_date' => $creationDate,
                'last_updated_date' => $lastUpdatedDate,
            ]);

            if ($contact->save()) {
                $this->successCount++;

                if (!empty($this->mailingListIds)) {
                    $contact->mailingLists()->attach($this->mailingListIds, [
                        'status' => 'subscribed',
                        'subscribed_at' => now(),
                    ]);
                }

                return $contact;
            }
        } catch (\Exception $e) {
            Log::error('Contact creation error: ' . $e->getMessage(), $row);
            $this->invalidEmails[] = [
                'row' => $this->currentRow - 1,
                'email' => $row['email'],
                'reason' => 'Failed to save: ' . $e->getMessage()
            ];
        }

        return null;
    }

    protected function parseCustomFields($customFields)
    {
        if (empty($customFields)) return [];

        if (is_array($customFields)) return $customFields;

        try {
            // Handle both JSON strings and other formats
            if (is_string($customFields)) {
                $decoded = json_decode($customFields, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
                
                // If not valid JSON, try to parse as key-value pairs
                if (strpos($customFields, ':') !== false) {
                    $pairs = explode(',', $customFields);
                    $result = [];
                    foreach ($pairs as $pair) {
                        $parts = explode(':', $pair, 2);
                        if (count($parts) === 2) {
                            $key = trim($parts[0], " \t\n\r\0\x0B\"'");
                            $value = trim($parts[1], " \t\n\r\0\x0B\"'");
                            $result[$key] = $value;
                        }
                    }
                    return $result;
                }
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('Custom fields parsing error: ' . $e->getMessage(), ['fields' => $customFields]);
            return [];
        }
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,inactive,pending',
            'source' => 'nullable|string|max:100',
            'opt_in_date' => 'nullable',
            'opt_in_confirmation' => 'nullable',
            'custom_fields' => 'nullable',
            'creation_date' => 'nullable',
            'last_updated_date' => 'nullable',
        ];
    }

    public function getExistingEmails(): array { return $this->existingEmails; }
    public function getInvalidEmails(): array { return $this->invalidEmails; }
    public function getEmptyData(): array { return $this->emptyData; }
    public function getSuccessCount(): int { return $this->successCount; }
    public function getTotalErrors(): int {
        return count($this->existingEmails) + count($this->invalidEmails) + count($this->emptyData);
    }

    public function chunkSize(): int { return 100; }
}