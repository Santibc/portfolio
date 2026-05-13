<?php

namespace App\Services;

use App\Models\ServiceBlockedZone;

class ServiceAvailabilityService
{
    /**
     * Verifica si una ubicación está dentro de zonas bloqueadas.
     *
     * @param float|null $lat
     * @param float|null $lng
     * @param string|null $postcode
     * @param string|null $suburb
     * @return array{allowed:bool, reason:?string, matched_zone:?array}
     */
    public function check(?float $lat, ?float $lng, ?string $postcode, ?string $suburb): array
    {
        // 1. Verificar postcode bloqueado
        if (!empty($postcode)) {
            $blocked = ServiceBlockedZone::active()
                ->postcodes()
                ->where('postcode', trim($postcode))
                ->first();

            if ($blocked) {
                return [
                    'allowed' => false,
                    'reason' => "We currently don't service the postcode {$postcode}.",
                    'matched_zone' => $blocked->only(['id', 'name', 'type']),
                ];
            }
        }

        // 2. Verificar suburb bloqueado (case-insensitive)
        if (!empty($suburb)) {
            $blocked = ServiceBlockedZone::active()
                ->suburbs()
                ->whereRaw('LOWER(suburb) = ?', [strtolower(trim($suburb))])
                ->first();

            if ($blocked) {
                return [
                    'allowed' => false,
                    'reason' => "We currently don't service the suburb of {$suburb}.",
                    'matched_zone' => $blocked->only(['id', 'name', 'type']),
                ];
            }
        }

        // 3. Verificar polígonos (point-in-polygon)
        if ($lat !== null && $lng !== null) {
            $polygons = ServiceBlockedZone::active()->polygons()->get();

            foreach ($polygons as $zone) {
                $coords = $zone->polygon_coordinates;
                if (!is_array($coords) || count($coords) < 3) {
                    continue;
                }

                if ($this->pointInPolygon($lat, $lng, $coords)) {
                    return [
                        'allowed' => false,
                        'reason' => "We currently don't service this area ({$zone->name}).",
                        'matched_zone' => $zone->only(['id', 'name', 'type']),
                    ];
                }
            }
        }

        return [
            'allowed' => true,
            'reason' => null,
            'matched_zone' => null,
        ];
    }

    /**
     * Algoritmo ray-casting para determinar si un punto está dentro de un polígono.
     *
     * @param float $lat
     * @param float $lng
     * @param array $polygon Array de {lat, lng}
     * @return bool
     */
    private function pointInPolygon(float $lat, float $lng, array $polygon): bool
    {
        $inside = false;
        $n = count($polygon);
        $j = $n - 1;

        for ($i = 0; $i < $n; $i++) {
            $vi = $polygon[$i];
            $vj = $polygon[$j];

            $latI = (float) ($vi['lat'] ?? 0);
            $lngI = (float) ($vi['lng'] ?? 0);
            $latJ = (float) ($vj['lat'] ?? 0);
            $lngJ = (float) ($vj['lng'] ?? 0);

            $intersect = (($latI > $lat) !== ($latJ > $lat))
                && ($lng < ($lngJ - $lngI) * ($lat - $latI) / (($latJ - $latI) ?: 1e-12) + $lngI);

            if ($intersect) {
                $inside = !$inside;
            }

            $j = $i;
        }

        return $inside;
    }
}
