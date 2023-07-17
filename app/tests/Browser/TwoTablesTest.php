<?php

namespace Tests\Browser;

use App\Models\Company;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TwoTablesTest extends DuskTestCase
{
    /** @test */
    public function it_shows_both_tables()
    {
        $this->browse(function (Browser $browser) {
            $companies = Company::query()
                ->select(['id', 'name', 'address'])
                ->orderBy('name')
                ->get();

            $users = User::query()
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get();

            $browser->visit('/two-tables/eloquent')
                ->waitFor('table')
                // Table with companies
                ->within('@table-companies', function (Browser $browser) use ($companies) {
                    $browser
                        ->assertSeeIn('tr:first-child td:nth-child(1)', $companies->get(0)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $companies->get(0)->address)
                        ->assertSeeIn('tr:last-child td:nth-child(1)', $companies->get(9)->name)
                        ->assertSeeIn('tr:last-child td:nth-child(2)', $companies->get(9)->address);
                })

                // Table with users
                ->within('@table-users', function (Browser $browser) use ($users) {
                    $browser
                        ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $users->get(0)->email)
                        ->assertSeeIn('tr:last-child td:nth-child(1)', $users->get(9)->name)
                        ->assertSeeIn('tr:last-child td:nth-child(2)', $users->get(9)->email);
                });
        });
    }

    /** @test */
    public function it_can_change_pages_independently()
    {
        $this->browse(function (Browser $browser) {
            $companies = Company::query()
                ->select(['id', 'name', 'address'])
                ->orderBy('name')
                ->get();

            $users = User::query()
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get();

            $browser->visit('/two-tables/eloquent')
                ->waitFor('table')
                ->within('@table-companies', function (Browser $browser) use ($companies) {
                    $browser
                        ->click('@pagination-2')
                        ->waitForText($companies->get(10)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(1)', $companies->get(10)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $companies->get(10)->address);
                })
                ->within('@table-users', function (Browser $browser) use ($users) {
                    $browser
                        ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $users->get(0)->email)
                        ->click('@pagination-3')
                        ->waitForText($users->get(20)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(20)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $users->get(20)->email);
                })
                ->within('@table-companies', function (Browser $browser) use ($companies) {
                    $browser
                        ->assertSeeIn('tr:first-child td:nth-child(1)', $companies->get(10)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $companies->get(10)->address);
                });
        });
    }

    /** @test */
    public function it_can_sort_independently()
    {
        $this->browse(function (Browser $browser) {
            $companies = Company::query()
                ->select(['id', 'name', 'address'])
                ->orderBy('name')
                ->get();

            $users = User::query()
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get();

            $usersByEmail = $users->sortBy->email->values();

            $browser->visit('/two-tables/spatie')
                ->waitFor('table')
                ->within('@table-companies', function (Browser $browser) use ($companies) {
                    $browser
                        ->click('@sort-name')
                        ->waitForText($companies->get(99)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(1)', $companies->get(99)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $companies->get(99)->address);
                })
                ->within('@table-users', function (Browser $browser) use ($users, $usersByEmail) {
                    $browser
                        ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $users->get(0)->email)
                        ->click('@sort-email')
                        ->waitForText($usersByEmail->get(0)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(1)', $usersByEmail->get(0)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $usersByEmail->get(0)->email);
                })
                ->within('@table-companies', function (Browser $browser) use ($companies) {
                    $browser
                        ->assertSeeIn('tr:first-child td:nth-child(1)', $companies->get(99)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $companies->get(99)->address);
                });
        });
    }

    /** @test */
    public function it_can_toggle_independently()
    {
        $this->browse(function (Browser $browser) {
            $companies = Company::query()
                ->select(['id', 'name', 'address'])
                ->orderBy('name')
                ->limit(10)
                ->get();

            $users = User::query()
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->limit(10)
                ->get();

            $browser->visit('/two-tables/spatie')
                ->waitFor('table')
                ->within('@table-companies', function (Browser $browser) use ($companies) {
                    $browser
                        ->click('@columns-dropdown')
                        ->click('@toggle-column-name')
                        ->waitUntilMissingText($companies->get(0)->name);
                })
                ->within('@table-users', function (Browser $browser) use ($users) {
                    $browser
                        ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $users->get(0)->email)
                        ->click('@columns-dropdown')
                        ->click('@toggle-column-name')
                        ->waitUntilMissingText($users->get(0)->name);
                });
        });
    }

    /** @test */
    public function it_can_search_independently()
    {
        $this->browse(function (Browser $browser) {
            $companies = Company::query()
                ->select(['id', 'name', 'address'])
                ->orderBy('name')
                ->get();

            $users = User::query()
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->get();

            $companies->last()->update([
                'name'    => 'Protone Media B.V.',
                'address' => 'The Netherlands',
            ]);

            $users->last()->update([
                'name'  => 'Baljet Pascal',
                'email' => 'pascal@pascal.pascal',
            ]);

            $browser->visit('/two-tables/spatie')
                ->waitFor('table')
                ->within('@table-companies', function (Browser $browser) use ($companies) {
                    $browser
                        ->click('@add-search-row-dropdown')
                        ->click('@add-search-row-name')
                        ->type('name', $companies->get(99)->name)
                        ->waitUntilMissingText($companies->get(0)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $companies->get(99)->address);
                })
                ->within('@table-users', function (Browser $browser) use ($users) {
                    $browser
                        ->assertSeeIn('tr:first-child td:nth-child(1)', $users->get(0)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $users->get(0)->email)
                        ->click('@add-search-row-dropdown')
                        ->click('@add-search-row-name')
                        ->type('name', $users->get(99)->name)
                        ->waitUntilMissingText($users->get(0)->name)
                        ->assertSeeIn('tr:first-child td:nth-child(2)', $users->get(99)->email);
                });
        });
    }
}
