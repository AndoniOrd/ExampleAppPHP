<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\BounceType; // Asegúrate de que este namespace es correcto

class CampaignReport extends Model
{
    use HasFactory;
    // Especifica el nombre de la tabla si no sigue la convención (plural del modelo)
    protected $table = 'campaign_reports';

    // Define los campos que pueden asignarse masivamente
    protected $fillable = [
        'campaign_planning_id',
        'total_recipients',
        'successful_deliveries',
        'hard_bounces',
        'soft_bounces',
        'opens_count',       // Corrected from 'opens'
        'opens_unique',      // Added
        'clicks_count',      // Corrected from 'clicks'
        'clicks_unique',     // Added
        'click_to_open_rate',
        'unsubscribes',
        'spam_complaints',
        'device_statistics',
        'geographical_data',
        'time_based_metrics',
        'engagement_score'
    ];

    // Agregamos el cast para que el campo 'bounces' se convierta en una instancia del enum BounceType
    protected $casts = [
        'bounces' => BounceType::class,
    ];

    // Ejemplo de relación: un CampaignReport pertenece a un CampaignPlanning
    public function campaignPlanning()
    {
        return $this->belongsTo(CampaignPlanning::class, 'campaign_planning_id');
    }
}
