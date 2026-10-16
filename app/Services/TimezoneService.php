<?php

namespace App\Services;

use DateTimeZone;
use Illuminate\Http\Request;

/**
 * Works out where a request comes from using the location headers Cloudflare
 * adds in front of the app. CF-Timezone needs the "Add visitor location
 * headers" managed transform; CF-IPCountry is sent by default.
 */
class TimezoneService
{
    public function detect(Request $request, ?string $current = null): ?string
    {
        $timezone = $request->header('CF-Timezone');
        if ($timezone && $this->isValid($timezone)) {
            return $timezone;
        }

        $country = strtoupper((string) $request->header('CF-IPCountry'));
        if ( ! preg_match('/^[A-Z]{2}$/', $country)) {
            return null; // missing, or Cloudflare's XX/T1 for unknown and Tor
        }

        $zones = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country);

        if (in_array($current, $zones, true)) {
            return $current;
        }

        // Zones that keep the same clock all year are interchangeable, e.g.
        // Germany's Berlin and Busingen. A country with really different
        // clocks (the US, or Spain with the Canary Islands) can't pin one down.
        return $zones && $this->shareOneClock($zones) ? $zones[0] : null;
    }

    /**
     * @param string[] $zones
     */
    private function shareOneClock(array $zones): bool
    {
        $year = (int) date('Y');
        $clocks = array_map(function (string $zone) use ($year) {
            $timezone = new DateTimeZone($zone);

            return $timezone->getOffset(new \DateTimeImmutable("$year-01-15", $timezone))
                . '/' . $timezone->getOffset(new \DateTimeImmutable("$year-07-15", $timezone));
        }, $zones);

        return count(array_unique($clocks)) === 1;
    }

    public function isValid(?string $timezone): bool
    {
        return $timezone !== null && in_array($timezone, DateTimeZone::listIdentifiers(), true);
    }
}
