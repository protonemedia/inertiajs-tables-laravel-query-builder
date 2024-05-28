<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CustomTableTest extends DuskTestCase
{
    /** @test */
    public function it_has_a_header_and_body_and_pagination()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/custom')
                ->waitFor('table')
                ->assertSeeIn('th:nth-child(1)', 'User')
                ->assertSeeIn('tr:first-child td:nth-child(1)', User::first()->name)
                ->assertPresent('@pagination-1');
        });
    }
}
