<?php

namespace Tests\Unit;

use App\Services\TimezoneService;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class TimezoneServiceTest extends TestCase
{
    private function request(array $headers): Request
    {
        $request = Request::create('/');
        foreach ($headers as $name => $value) {
            $request->headers->set($name, $value);
        }

        return $request;
    }

    public function test_the_cloudflare_time_zone_header_wins(): void
    {
        $this->assertSame('America/Chicago', (new TimezoneService)->detect($this->request(['CF-Timezone' => 'America/Chicago', 'CF-IPCountry' => 'AT'])));
    }

    public function test_an_invalid_time_zone_header_falls_back_to_the_country(): void
    {
        $this->assertSame('Europe/Vienna', (new TimezoneService)->detect($this->request(['CF-Timezone' => 'Mars/Olympus', 'CF-IPCountry' => 'AT'])));
    }

    public function test_a_country_with_one_time_zone_gives_that_zone(): void
    {
        $this->assertSame('Europe/Berlin', (new TimezoneService)->detect($this->request(['CF-IPCountry' => 'de'])));
    }

    public function test_a_country_whose_zones_differ_gives_nothing(): void
    {
        // The Canary Islands are an hour behind mainland Spain.
        $this->assertNull((new TimezoneService)->detect($this->request(['CF-IPCountry' => 'ES'])));
        $this->assertSame('Atlantic/Canary', (new TimezoneService)->detect($this->request(['CF-IPCountry' => 'ES']), 'Atlantic/Canary'));
    }

    public function test_a_country_with_several_time_zones_keeps_a_matching_current_zone(): void
    {
        $request = $this->request(['CF-IPCountry' => 'US']);

        $this->assertSame('America/Denver', (new TimezoneService)->detect($request, 'America/Denver'));
        $this->assertNull((new TimezoneService)->detect($request, 'Europe/Vienna'));
        $this->assertNull((new TimezoneService)->detect($request));
    }

    public function test_unknown_or_missing_locations_give_nothing(): void
    {
        $this->assertNull((new TimezoneService)->detect($this->request(['CF-IPCountry' => 'XX'])));
        $this->assertNull((new TimezoneService)->detect($this->request(['CF-IPCountry' => 'T1'])));
        $this->assertNull((new TimezoneService)->detect($this->request([])));
    }
}
