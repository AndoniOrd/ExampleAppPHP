<?php

namespace Database\Factories;

use App\Models\CampaignReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignReportFactory extends Factory
{
    // Especifica el modelo asociado a la factory
    protected $model = CampaignReport::class;

    public function definition()
    {
        // Genera un total de receptores aleatorio
        $totalRecipients = $this->faker->numberBetween(100, 10000);
        // Asegúrate de que los deliveries sean menor o iguales a totalRecipients
        $successfulDeliveries = $this->faker->numberBetween(50, $totalRecipients);

        return [
            // Para la clave foránea, en un entorno de pruebas podrías usar un UUID o vincularlo a otra factory
            'campaign_ID'              => $this->faker->uuid,
            'total_recipients'         => $totalRecipients,
            'successful_deliveries'    => $successfulDeliveries,
            'bounces'                  => $this->faker->randomElement(['hard', 'soft']),
            'opens'                    => $this->faker->numberBetween(10, $successfulDeliveries),
            'clicks'                   => $this->faker->numberBetween(5, 50),
            'click_to_open_rate'       => $this->faker->randomFloat(2, 0, 1),
            'unsubscribes'             => $this->faker->randomFloat(2, 0, 0.1),
            'complaints_spam_reports'  => $this->faker->randomFloat(2, 0, 0.05),
            // Simulamos datos en formato JSON para los campos de tipo TEXT
            'device_statistics'        => json_encode([
                                            'desktop' => $this->faker->numberBetween(1, 100),
                                            'mobile'  => $this->faker->numberBetween(1, 100)
                                        ]),
            'geographical_data'        => json_encode([
                                            $this->faker->country => $this->faker->numberBetween(1, 100)
                                        ]),
            'time_based_metrics'       => json_encode([
                                            'morning' => $this->faker->numberBetween(1, 100),
                                            'evening' => $this->faker->numberBetween(1, 100)
                                        ]),
            'engagement_score'         => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
