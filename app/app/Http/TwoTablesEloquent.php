<?php

namespace App\Http;

use App\Models\Company;
use App\Models\User;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class TwoTablesEloquent
{
    public function __invoke()
    {
        InertiaTable::defaultGlobalSearch();

        $companies = Company::query()
            ->orderBy('name')
            ->paginate(perPage: 10, pageName: 'companiesPage')
            ->withQueryString();

        $users = User::query()
            ->orderBy('name')
            ->paginate(perPage: 10, pageName: 'usersPage')
            ->withQueryString();

        return Inertia::render('TwoTables', [
            'companies' => $companies,
            'users'     => $users,
        ])->table(function (InertiaTable $inertiaTable) {
            $inertiaTable
                ->name('users')
                ->pageName('usersPage')
                ->column('name')
                ->column('email');
        })->table(function (InertiaTable $inertiaTable) {
            $inertiaTable
                ->name('companies')
                ->pageName('companiesPage')
                ->column('name')
                ->column('address');
        });
    }
}
