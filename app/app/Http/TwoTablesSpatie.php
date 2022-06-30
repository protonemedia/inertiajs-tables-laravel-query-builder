<?php

namespace App\Http;

use App\Models\Company;
use App\Models\User;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\QueryBuilder;

class TwoTablesSpatie
{
    public function __invoke()
    {
        InertiaTable::updateQueryBuilderParameters('companies');

        $companies = QueryBuilder::for(Company::query())
            ->defaultSort('name')
            ->allowedSorts(['name', 'email'])
            ->allowedFilters(['name', 'email'])
            ->paginate(perPage: 10, pageName: 'companiesPage')
            ->withQueryString();

        InertiaTable::updateQueryBuilderParameters('users');

        $users = QueryBuilder::for(User::query())
            ->defaultSort('name')
            ->allowedSorts(['name', 'email'])
            ->allowedFilters(['name', 'email'])
            ->paginate(perPage: 10, pageName: 'usersPage')
            ->withQueryString();

        return Inertia::render('TwoTables', [
            'companies' => $companies,
            'users'     => $users,
        ])->table(function (InertiaTable $inertiaTable) {
            $inertiaTable
                ->name('users')
                ->pageName('usersPage')
                ->searchInput('name')
                ->searchInput('email')
                ->defaultSort('name')
                ->column(key: 'name', sortable: true)
                ->column(key: 'email', sortable: true);
        })->table(function (InertiaTable $inertiaTable) {
            $inertiaTable
                ->name('companies')
                ->pageName('companiesPage')
                ->searchInput('name')
                ->defaultSort('name')
                ->column(key: 'name', sortable: true)
                ->column(key: 'address', sortable: true);
        });
    }
}
