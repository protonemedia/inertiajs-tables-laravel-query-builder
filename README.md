# Inertia.js Tables for Laravel Query Builder

[![Latest Version on NPM](https://img.shields.io/npm/v/@protonemedia/inertiajs-tables-laravel-query-builder.svg?style=flat-square)](https://npmjs.com/package/@protonemedia/inertiajs-tables-laravel-query-builder)
[![npm](https://img.shields.io/npm/dt/@protonemedia/inertiajs-tables-laravel-query-builder.svg?style=flat-square)](https://www.npmjs.com/package/@protonemedia/inertiajs-tables-laravel-query-builder)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/protonemedia/inertiajs-tables-laravel-query-builder.svg?style=flat-square)](https://packagist.org/packages/protonemedia/inertiajs-tables-laravel-query-builder)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![run-tests](https://github.com/protonemedia/inertiajs-tables-laravel-query-builder/workflows/php/badge.svg)

This package provides a *DataTables-like* experience for [Inertia.js](https://inertiajs.com/) with support for searching, filtering, sorting, toggling columns, and pagination. It generates URLs that can be consumed by Spatie's excellent [Laravel Query Builder](https://github.com/spatie/laravel-query-builder) package, with no additional logic needed. The components are styled with [Tailwind CSS 3.0](https://tailwindcss.com/), but it's fully customizable with slots. The data refresh logic is based on Inertia's [Ping CRM demo](https://github.com/inertiajs/pingcrm).

![Inertia.js Table for Laravel Query Builder](https://user-images.githubusercontent.com/8403149/113340981-e3863680-932c-11eb-8017-7a6588916508.mp4)

## Sponsorship Support

We proudly support the community by developing Laravel packages and giving them away for free. Keeping track of issues and pull requests takes time, but we're happy to help! If this package saves you time or if you're relying on it professionally, please consider [supporting the maintenance and development](https://github.com/sponsors/pascalbaljet).

## Features

* Auto-fill: auto generates `thead` and `tbody` with support for custom cells
* Global Search
* Search per field
* Select filters
* Toggle columns
* Sort columns
* Pagination (support for Eloquent/API Resource/Simple/Cursor)
* Automatically updates the query string (by using [Inertia's replace](https://inertiajs.com/manual-visits#browser-history) feature)

## Compatibility

* [Vue 3](https://v3.vuejs.org/guide/installation.html)
* [Laravel 9](https://laravel.com/)
* [Inertia.js](https://inertiajs.com/)
* [Tailwind CSS v3](https://tailwindcss.com/) + [Forms plugin](https://github.com/tailwindlabs/tailwindcss-forms)
* PHP 8.0+

## Installation

You need to install both the server-side package and the client-side package. Note that this package is only compatible with Laravel 9, Vue 3.0, and requires the Tailwind Forms plugin.

### Server-side installation (Laravel)

You can install the package via composer:

```bash
composer require protonemedia/inertiajs-tables-laravel-query-builder
```

The package will automatically register the Service Provider which provides a `table` method you can use on an Interia Response.

#### Search fields

With the `searchInput` method, you can specify which attributes are searchable. Search queries are passed to the URL query as a `filter`. This integrates seamlessly with the [filtering feature](https://spatie.be/docs/laravel-query-builder/v5/features/filtering) of the Laravel Query Builder package.


Though it's enough to pass in the column key, you may specify a custom label and default value.

```php
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

Inertia::render('Page/Index')->table(function (InertiaTable $table) {
    $table->searchInput('name');

    $table->searchInput(
        key: 'framework',
        label: 'Find your framework',
        defaultValue: 'Laravel'
    );
});
```

#### Select Filters

Select Filters are similar to search fields but use a `select` element instead of an `input` element. This way, you can present the user a predefined set of options. Under the hood, this uses the same filtering feature of the Laravel Query Builder package.

The `selectFilter` method requires two arguments: the key, and a key-value array with the options.

```php
Inertia::render('Page/Index')->table(function (InertiaTable $table) {
    $table->selectFilter('language_code', [
        'en' => 'Engels',
        'nl' => 'Nederlands',
    ]);
});
```

The `selectFilter` will, by default, add a *no filter* option to the array. You may disable this or specify a custom label for it.

```php
Inertia::render('Page/Index')->table(function (InertiaTable $table) {
    $table->selectFilter(
        key: 'language_code',
        options: $languages,
        label: 'Language',
        defaultValue: 'nl',
        noFilterOption: true
        noFilterOptionLabel: 'All languages'
    );
});
```

#### Columns

With the `column` method, you can specify which columns you want to be toggleable, sortable, and searchable. You must pass in at least a key or label for each column.

```php
Inertia::render('Page/Index')->table(function (InertiaTable $table) {
    $table->column('name', 'User Name');

    $table->column(
        key: 'name',
        label: 'User Name',
        canBeHidden: true,
        hidden: false,
        sortable: true,
        searchable: true
    );
});
```

The `searchable` option is a shortcut to the `searchInput` method. The example below will essentially call `$table->searchInput('name', 'User Name')`.

#### Global Search

You may enable Global Search with the `withGlobalSearch` method, and optionally specify a placeholder.

```php
Inertia::render('Page/Index')->table(function (InertiaTable $table) {
    $table->withGlobalSearch();

    $table->withGlobalSearch('Search through the data...');
});
```

If you want to enable Global Search for every table by default, you may use the static `defaultGlobalSearch` method, for example, in the `AppServiceProvider` class:

```php
InertiaTable::defaultGlobalSearch();
InertiaTable::defaultGlobalSearch('Default custom placeholder');
InertiaTable::defaultGlobalSearch(false); // disable
```

#### Example controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserIndexController
{
    public function __invoke()
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

        $users = QueryBuilder::for(User::class)
            ->defaultSort('name')
            ->allowedSorts(['name', 'email', 'language_code'])
            ->allowedFilters(['name', 'email', 'language_code', $globalSearch])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
        ])->table(function (InertiaTable $table) {
            $table
              ->withGlobalSearch()
              ->defaultSort('name')
              ->column(key: 'name', searchable: true, sortable: true, canBeHidden: false)
              ->column(key: 'email', searchable: true, sortable: true)
              ->column(key: 'language_code', label: 'Language')
              ->column(label: 'Actions')
              ->selectFilter(key: 'language_code', label: 'Language', options: [
                  'en' => 'English',
                  'nl' => 'Dutch',
              ]);
    }
}
```

### Client-side installation (Inertia)

You can install the package via either `npm` or `yarn`:

```bash
npm install @protonemedia/inertiajs-tables-laravel-query-builder --save

yarn add @protonemedia/inertiajs-tables-laravel-query-builder
```

Add the repository path to the `content` array of your [Tailwind configuration file](https://tailwindcss.com/docs/content-configuration). This ensures that the styling also works on production builds.

```js
module.exports = {
  content: [
    './node_modules/@protonemedia/inertiajs-tables-laravel-query-builder/**/*.{js,vue}',
  ]
}
```

#### Table component

To use the `Table` component and all its related features, you must import the `Table` component and pass the `users` data to the component.

```vue
<script setup>
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";

defineProps(["users"])
</script>

<template>
  <Table :resource="users" />
</template>
```

The `resource` property automatically detects the data and additional pagination meta data. You may also pass this manually to the component with the `data` and `meta` properties:

```vue
<template>
  <Table :data="users.data" :meta="users.meta" />
</template>
```

If you want to manually render the table, like in v1 of this package, you may use the `head` and `body` slot. Additionally, you can still use the `meta` property to render the paginator.

```vue
<template>
  <Table :meta="users">
    <template #head>
      <tr>
        <th>User</th>
      </tr>
    </template>

    <template #body>
      <tr
        v-for="(user, key) in users.data"
        :key="key"
      >
        <td>{{ user.name }}</td>
      </tr>
    </template>
  </Table>
</template>
```

The `Table` has some additional properties to tweak its front-end behaviour.

```vue
<template>
  <Table
    :striped="true"
    :prevent-overlapping-requests="false"
    :input-debounce-ms="1000"
    :prevent-scroll="true"
  />
</template>
```

| Property | Description | Default |
| --- | --- | --- |
| striped | Adds a *striped* layout to the table. | `false` |
| preventOverlappingRequests | Cancels a previous visit on new user input to prevent an inconsistent state. | `true` |
| inputDebounceMs | Number of ms to wait before refreshing the table on user input. | 350 |
| preventScroll | Configures the [Scroll preservation](https://inertiajs.com/scroll-management#scroll-preservation) behavior. You may also pass `table-top` to this property to scroll to the top of the table on new data. | false |

#### Custom column cells

When using *auto-fill*, you may want to transform the presented data for a specific column while leaving the other columns untouched. For this, you may use a cell template. This example is taken from the [Example Controller](#example-controller) above.

```vue
<template>
  <Table :resource="users">
    <template #cell(actions)="{ item: user }">
      <a :href="`/users/${user.id}/edit`">
        Edit
      </a>
    </template>
  </Table>
</template>
```

#### Multiple tables per page

You may want to use more than one table component per page. Displaying the data is easy, but using features like filtering, sorting, and pagination requires a slightly different setup. For example, by default, the `page` query key is used for paginating the data set, but now you want two different keys for each table. Luckily, this package takes care of that and even provides a helper method to support Spatie's query package. To get this to work, you need to *name* your tables.

Let's take a look at Spatie's `QueryBuilder`. In this example, there's a table for the companies and a table for the users. We name the tables accordingly. So first, call the static `updateQueryBuilderParameters` method to tell the package to use a different set of query parameters. Now, `filter` becomes `companies_filter`, `column` becomes `companies_column`, and so forth. Secondly, change the `pageName` of the database paginator.

```php
InertiaTable::updateQueryBuilderParameters('companies');

$companies = QueryBuilder::for(Company::query())
    ->defaultSort('name')
    ->allowedSorts(['name', 'email'])
    ->allowedFilters(['name', 'email'])
    ->paginate(pageName: 'companiesPage')
    ->withQueryString();

InertiaTable::updateQueryBuilderParameters('users');

$users = QueryBuilder::for(User::query())
    ->defaultSort('name')
    ->allowedSorts(['name', 'email'])
    ->allowedFilters(['name', 'email'])
    ->paginate(pageName: 'usersPage')
    ->withQueryString();
```

Then, we need to apply these two changes to the `InertiaTable` class. There's a `name` and `pageName` method to do so.

```php
return Inertia::render('TwoTables', [
    'companies' => $companies,
    'users'     => $users,
])->table(function (InertiaTable $inertiaTable) {
    $inertiaTable
        ->name('users')
        ->pageName('usersPage')
        ->defaultSort('name')
        ->column(key: 'name', searchable: true)
        ->column(key: 'email', searchable: true);
})->table(function (InertiaTable $inertiaTable) {
    $inertiaTable
        ->name('companies')
        ->pageName('companiesPage')
        ->defaultSort('name')
        ->column(key: 'name', searchable: true)
        ->column(key: 'address', searchable: true);
});
```

Lastly, pass the correct `name` property to each table in the Vue template. Optionally, you may set the `preserve-scroll` property to `table-top`. This makes sure to scroll to the top of the table on new data. For example, when changing the page of the *second* table, you want to scroll to the top of the table, instead of the top of the page.

```vue
<script setup>
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";

defineProps(["companies", "users"])
</script>

<template>
  <Table
    :resource="companies"
    name="companies"
    preserve-scroll="table-top"
  />

  <Table
    :resource="users"
    name="users"
    preserve-scroll="table-top"
  />
</template>
```

#### Table.vue slots

The `Table.vue` has several slots that you can use to inject your own implementations.

| Slot | Description |
| --- | --- |
| tableFilter | The location of the button + dropdown to select filters. |
| tableGlobalSearch | The location of the input element that handles the global search. |
| tableReset | The location of the button that resets the table. |
| tableAddSearchRow | The location of the button + dropdown to add additional search rows. |
| tableColumns | The location of the button + dropdown to toggle columns. |
| tableSearchRows | The location of the input elements that handle the additional search rows. |
| tableWrapper | The component that *wraps* the table element, handling overflow, shadow, padding, etc. |
| table | The actual table element. |
| head | The location of the table header. |
| body | The location of the table body.  |
| pagination | The location of the paginator. |

Each slot is provided with props to interact with the parent `Table` component.

```vue
<template>
  <Table>
    <template v-slot:tableGlobalSearch="slotProps">
      <input
        placeholder="Custom Global Search Component..."
        @input="slotProps.onChange($event.target.value)"
      />
    </template>
  </Table>
</template>
```

## Testing

A huge [Laravel Dusk](https://laravel.com/docs/9.x/dusk) E2E test-suite can be found in the `app` directory. Here you'll find a Laravel + Inertia application.

```bash
cd app
cp .env.example .env
composer install
npm install
npm run production
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan dusk:chrome-driver
php artisan serve
php artisan dusk
```

## Upgrading from v1

### Server-side

* The `addColumn` method has been renamed to `column`.
* The `addFilter` method has been renamed to `selectFilter`.
* The `addSearch` method has been renamed to `searchInput`.
* For all renamed methods, check out the arguments as some have been changed.
* The `addColumns` and `addSearchRows` methods have been removed.
* Global Search is not enabled by default anymore.

### Client-side

* The `InteractsWithQueryBuilder` mixin has been removed and is no longer needed.
* The `Table` component no longer needs the `filters`, `search`, `columns`, and `on-update` properties.
* When using a custom `thead` or `tbody` slot, you need to provide [the styling](https://github.com/protonemedia/inertiajs-tables-laravel-query-builder/blob/c8e21649ad372d309eeb62a8f771aa4c7cd0089e/js/Tailwind2/Table.vue#L1) manually.
* When using a custom `thead`, the `showColumn` method has been renamed to `show`.
* The templates and logic of the components are not separated anymore. Use slots to inject your own implementations.

## v2.1 Roadmap

* Boolean filters
* Date filters
* Date range filters
* Switch to Vite for the demo app

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Other Laravel packages

* [`Laravel Analytics Event Tracking`](https://github.com/protonemedia/laravel-analytics-event-tracking): Laravel package to easily send events to Google Analytics.
* [`Laravel Blade On Demand`](https://github.com/protonemedia/laravel-blade-on-demand): Laravel package to compile Blade templates in memory.
* [`Laravel Cross Eloquent Search`](https://github.com/protonemedia/laravel-cross-eloquent-search): Laravel package to search through multiple Eloquent models.
* [`Laravel Eloquent Scope as Select`](https://github.com/protonemedia/laravel-eloquent-scope-as-select): Stop duplicating your Eloquent query scopes and constraints in PHP. This package lets you re-use your query scopes and constraints by adding them as a subquery.
* [`Laravel Eloquent Where Not`](https://github.com/protonemedia/laravel-eloquent-where-not): This Laravel package allows you to flip/invert an Eloquent scope, or really any query constraint.
* [`Laravel FFMpeg`](https://github.com/protonemedia/laravel-ffmpeg): This package provides an integration with FFmpeg for Laravel. The storage of the files is handled by Laravel's Filesystem.
* [`Laravel Form Components`](https://github.com/protonemedia/laravel-form-components): Blade components to rapidly build forms with Tailwind CSS Custom Forms and Bootstrap 4. Supports validation, model binding, default values, translations, includes default vendor styling and fully customizable!
* [`Laravel Mixins`](https://github.com/protonemedia/laravel-mixins): A collection of Laravel goodies.
* [`Laravel Verify New Email`](https://github.com/protonemedia/laravel-verify-new-email): This package adds support for verifying new email addresses: when a user updates its email address, it won't replace the old one until the new one is verified.
* [`Laravel Paddle`](https://github.com/protonemedia/laravel-paddle): Paddle.com API integration for Laravel with support for webhooks/events.
* [`Laravel WebDAV`](https://github.com/protonemedia/laravel-webdav): WebDAV driver for Laravel's Filesystem.

## Security

If you discover any security related issues, please email pascal@protone.media instead of using the issue tracker.

## Credits

- [Pascal Baljet](https://github.com/protonemedia)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
