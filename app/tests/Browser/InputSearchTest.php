<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InputSearchTest extends DuskTestCase
{
    /** @test */
    public function it_can_search_by_name_or_email()
    {
        $this->browse(function (Browser $browser) {
            User::first()->forceFill([
                'name'  => 'Pascal Baljet',
                'email' => 'pascal@protone.media',
            ])->save();

            $users = User::query()
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get();

            $browser->visit('/users/eloquent')
                ->waitFor('table')
                // First user
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                ->assertDontSee('Pascal Baljet')
                ->press('@add-search-row-dropdown')
                ->press('@add-search-row-name')
                ->type('name', 'Pascal Baljet')
                ->waitForText('pascal@protone.media')
                ->press('@remove-search-row-name')
                ->waitUntilMissingText('pascal@protone.media')
                ->press('@add-search-row-dropdown')
                ->press('@add-search-row-email')
                ->type('email', 'pascal@protone.media')
                ->waitForText('Pascal Baljet');
        });
    }
}
