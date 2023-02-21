<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ResetTest extends DuskTestCase
{
    /** @test */
    public function it_can_reset_toggled_columns()
    {
        $this->browse(function (Browser $browser) {
            $users = User::query()
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get();

            $browser->visit('/users/eloquent')
                ->waitFor('table')
                ->assertSeeIn('tr:first-child td:nth-child(2)', $users->get(0)->email)
                ->assertMissing('@reset-table')
                ->press('@columns-dropdown')
                ->press('@toggle-column-email')
                ->waitUntilMissingText($users->get(0)->email)
                ->press('@reset-table')
                ->waitForTextIn('tr:first-child td:nth-child(2)', $users->get(0)->email);
        });
    }

    /** @test */
    public function it_can_reset_select_filters()
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
                ->assertMissing('@reset-table')
                ->press('@filters-dropdown')
                ->select('language_code', 'nl')
                ->waitUntilMissingText($users->get(0)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $firstDutchUser->name)
                ->press('@reset-table')
                ->waitForText($users->get(0)->name);
        });
    }

    /** @test */
    public function it_can_reset_global_search()
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
                ->assertMissing('@reset-table')
                ->assertDontSee('Pascal Baljet')
                ->type('global', 'Pascal Baljet')
                ->waitForText('pascal@protone.media')
                ->press('@reset-table')
                ->waitUntilMissingText('pascal@protone.media');
        });
    }

    /** @test */
    public function it_can_reset_search_inputs()
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
                ->assertMissing('@reset-table')
                ->assertDontSee('Pascal Baljet')
                ->press('@add-search-row-dropdown')
                ->press('@add-search-row-name')
                ->type('name', 'Pascal Baljet')
                ->waitForText('pascal@protone.media')
                ->press('@reset-table')
                ->waitUntilMissingText('pascal@protone.media');
        });
    }

    /** @test */
    public function it_can_reset_the_sort()
    {
        $this->browse(function (Browser $browser) {
            $users = User::query()
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get();

            $usersByEmail = $users->sortBy->email->values();

            $browser->visit('/users/eloquent')
                ->waitFor('table')
                // First user
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(9)->name)
                ->assertMissing('@reset-table')

                // Sort desc
                ->press('@sort-name')
                ->waitUntilMissingText($users->get(0)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(99)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(90)->name)

                // Restore asc sort
                ->press('@reset-table')
                ->waitUntilMissingText($users->get(99)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(9)->name);
        });
    }

    public function it_can_reset_to_the_first_page()
    {
        $this->browse(function (Browser $browser) {
            $users = User::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $browser
                ->visit('/users/eloquent')
                ->waitFor('table')
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(9)->name)
                ->assertMissing('@reset-table')
                ->press('@pagination-next')
                ->waitUntilMissingText($users->get(0)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(10)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(19)->name)
                ->press('@reset-table')
                ->waitUntilMissingText($users->get(10)->name)
                ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(9)->name);
        });
    }
}
