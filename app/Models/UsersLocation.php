<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Log;

class UsersLocation extends Model
{
    use HasFactory;

    protected $table = 'users_locations';

    protected $fillable = [
        'latitude',
        'longitude',
        'user_id',
        'table_name',
        'table_id',
        'activity_type ',
        'table_type',
        'location_title',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // ✅ Check for missing coordinates
            if (empty($model->latitude) || empty($model->longitude)) {
                $model->location_title = 'Coordinates missing';
                return;
            }

            $maxAttempts = 5;
            $attempt = 0;

            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$model->latitude}&lon={$model->longitude}&accept-language=en";

            $options = [
                'http' => [
                    'header' => "User-Agent: dashi-snd 1.0\r\n"
                ]
            ];

            $context = stream_context_create($options);

            while ($attempt < $maxAttempts) {
                try {
                    $response = file_get_contents($url, false, $context);

                    if ($response === false) {
                        throw new \Exception("Failed to fetch data from Nominatim.");
                    }

                    $data = json_decode($response, true);

                    if (!isset($data['display_name'])) {
                        throw new \Exception("Invalid JSON structure from API.");
                    }

                    $model->location_title = $data['display_name'];
                    return; // Success
                } catch (\Exception $e) {
                    $attempt++;
                    Log::warning("Location fetch attempt {$attempt} failed: " . $e->getMessage());

                    if ($attempt < $maxAttempts) {
                        sleep(1); // Wait before retry
                    } else {
                        $model->location_title = 'Error fetching address';
                    }
                }
            }
        });

        // Define morph map for polymorphic relations
        Relation::morphMap([
            'attendences'   => 'App\Models\Attendence',
            'shops'         => 'App\Models\Shop',
            'shop_visits'   => 'App\Models\ShopVisit',
            'sale_orders'   => 'App\Models\SaleOrder',
        ]);
    }

    // Polymorphic relation
    public function location()
    {
        return $this->morphTo(null, 'table_name', 'table_id');
    }
}
