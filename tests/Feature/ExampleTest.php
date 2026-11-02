<?php

namespace Tests\Feature;

use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The Ruby module's public login page stands in for the absent
     * application-wide welcome route. Tenancy middlewares are bypassed the
     * same way the staff suite does - they require a tenant subdomain.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->withoutMiddleware([InitializeTenancyBySubdomain::class, PreventAccessFromCentralDomains::class]);

        $response = $this->get('r/login');

        $response->assertOk();
    }
}
