<?php

namespace App\Http;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserTableView
{
    public function __invoke($resource = false, $paginateMethod)
    {
        $globalSearch = AllowedFilter::callback('global', function ($query, $value) {
            $query->where(function ($query) use ($value) {
                Collection::wrap($value)->each(function ($value) use ($query) {
                    $query
                        ->orWhere('name', 'LIKE', "%{$value}%")
                        ->orWhere('email', 'LIKE', "%{$value}%");
                });
            });
        });

        $users = QueryBuilder::for(User::query())
            ->defaultSort('name')
            ->allowedSorts(['name', 'email', 'language_code'])
            ->allowedFilters(['name', 'email', 'language_code', $globalSearch])
            ->{$paginateMethod}(request()->query('perPage', 10))
            ->withQueryString();

        return Inertia::render('Users', [
            'users' => $resource ? UserResource::collection($users) : $users,
        ])->table(function (InertiaTable $table) {
            $table
                ->withGlobalSearch()
                ->defaultSort('name')
                ->column(key: 'name', searchable: true, sortable: true, canBeHidden: false)
                ->column(key: 'email', searchable: true, sortable: true)
                ->column(key: 'language_code', label: 'Language')
                ->column(label: 'Actions')
                ->selectFilter(key: 'language_code', options: [
                    'en' => 'English',
                    'nl' => 'Dutch',
                ], label: 'Language');
        });
    }
}
