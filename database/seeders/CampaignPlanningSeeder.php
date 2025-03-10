<?php

namespace Database\Seeders;

use App\Models\CampaignPlanning;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CampaignPlanningSeeder extends Seeder
{
    public function run()
    {
        CampaignPlanning::create([
            'name' => 'Spring Campaign',
            'description' => 'A campaign for the spring season.',
            'email_template_id' => 'template_1', // ID del template de correo
            'mailing_list_id' => 'list_1', // ID de la lista de correo
            'scheduled_time' => Carbon::now()->addDays(5), // Fecha programada para dentro de 5 días
            'time_zone' => 'UTC',
            'status_status_type' => 'scheduled',
            'creation_date' => Carbon::now()->subWeek(), // Fecha de creación hace 1 semana
            'scheduled_by' => '1', // ID del usuario que programó
            'send_from_email' => 'no-reply@example.com',
            'send_from_name' => 'Example Company',
            'reply_to_email' => 'support@example.com',
            'tracking_options' => 'opens', // O el valor que corresponda a tu enum
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}