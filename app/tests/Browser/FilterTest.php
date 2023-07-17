<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FilterTest extends DuskTestCase
{
    /** @test */
    public function it_can_use_select_filters()
    {
        $this->browse(function (Browser $browser) {
            User::orderBy('name')->first()->forceFill([
                'language_code' => 'en',
            ])->save();

            $users = User::query()
                ->orderBy('name')
                ->get();

            $firstDutchUser = $users->firstWhere('language_code', 'nl');

            $browser->visit('/users/eloquent')
                ->waitFor('table')
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                ->press('@filters-dropdown')
                ->select('language_code', 'nl')
                ->waitUntilMissingText($users->get(0)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $firstDutchUser->name);
        });
    }
}
