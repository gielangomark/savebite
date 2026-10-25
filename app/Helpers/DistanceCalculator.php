<?php

namespace App\Helpers;

class DistanceCalculator
{
    /**
     * Hitung jarak antara 2 koordinat menggunakan Haversine Formula (satuan: kilometer)
     */
    public static function calculate(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earth_radius = 6371; // Radius bumi dalam km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * asin(sqrt($a));

        return $earth_radius * $c;
    }

    /**
     * Format jarak dalam km dengan 1 desimal
     */
    public static function formatDistance(float $km): string
    {
        if ($km < 1) {
            return round($km * 1000) . ' m';
        }
        return number_format($km, 1, ',', '.') . ' km';
    }
}
