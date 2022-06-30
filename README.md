# Inertia.js Tables for Laravel Query Builder

[![Latest Version on NPM](https://img.shields.io/npm/v/@protonemedia/inertiajs-tables-laravel-query-builder.svg?style=flat-square)](https://npmjs.com/package/@protonemedia/inertiajs-tables-laravel-query-builder)
[![npm](https://img.shields.io/npm/dt/@protonemedia/inertiajs-tables-laravel-query-builder.svg?style=flat-square)](https://www.npmjs.com/package/@protonemedia/inertiajs-tables-laravel-query-builder)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/protonemedia/inertiajs-tables-laravel-query-builder.svg?style=flat-square)](https://packagist.org/packages/protonemedia/inertiajs-tables-laravel-query-builder)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![run-tests](https://github.com/protonemedia/inertiajs-tables-laravel-query-builder/workflows/php/badge.svg)

This package provides a *DataTables-like* experience for [Inertia.js](https://inertiajs.com/) with support for searching, filtering, sorting, toggling columns, and pagination. It generates URLs that can be consumed by Spatie's excellent [Laravel Query Builder](https://github.com/spatie/laravel-query-builder) package, with no additional logic needed. The components are styled with [Tailwind CSS 3.0](https://tailwindcss.com/), but it's fully customizable and you can bring your own components. The data refresh logic is based on Inertia's [Ping CRM demo](https://github.com/inertiajs/pingcrm).

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
* Pagination
* Automatically updates the query string (by using [Inertia's replace](https://inertiajs.com/manual-visits#browser-history) feature)

## Compatibility

* [Vue 3](https://v3.vuejs.org/guide/installation.html)
* [Laravel 9](https://laravel.com/)
* [Inertia.js](https://inertiajs.com/)
* [Tailwind CSS v3](https://tailwindcss.com/) + [Forms plugin](https://github.com/tailwindlabs/tailwindcss-forms)
* PHP 8.0+

## Installation

You need to install both the server-side package as well as the client-side package. Note that this package is only compatible with Laravel 9, Vue 3.0 and requires the Tailwind Forms plugin.

### Server-side installation (Laravel)

You can install the package via composer:

```bash
composer require protonemedia/inertiajs-tables-laravel-query-builder
```

The package will automatically register the Service Provider which provides a `table` method that you can use on an Interia Response.

#### Search fields

With the `searchInput` method, you can specify which attributes are searchable. Search queries are passed to the URL query as a `filter`. This integrates seamlessly with the [filtering feature](https://spatie.be/docs/laravel-query-builder/v5/features/filtering) of the Laravel Query Builder package.


Though it's enough to just pass in the column key, you may specify a custom label and default value.

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

Select Filters are similar to search fields, but they use a `select` element instead of an `input` element. This way, you can present the user a pre-defined set of options. Under the hood, this uses the same filtering feature of the Laravel Query Builder package.

The `selectFilter` method requires two arguments: the key, and a key-value array with the options.

```php
Inertia::render('Page/Index')->table(function (InertiaTable $table) {
    $table->selectFilter('language_code', [
        'en' => 'Engels',
        'nl' => 'Nederlands',
    ]);
});
```

By default, the `selectFilter` will add a *no filter* option to the array. You may disable this, or specify a custom label for it.

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

With the `column` method, you can specify which columns you want to be toggleable, sortable,and searchable. You need to pass in at least a key or label for each column.

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
                $query
                  ->orWhere('name', 'LIKE', "%{$value}%")
                  ->orWhere('email', 'LIKE', "%{$value}%");
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

Add the repository path to the `purge` array of your [Tailwind configuration file](https://tailwindcss.com/docs/optimizing-for-production#basic-usage). This ensures that the styling also works on production builds.

```js
module.exports = {
  purge: [
    './node_modules/@protonemedia/inertiajs-tables-laravel-query-builder/**/*.{js,vue}',
  ]
}
```

#### Table component

To use the `Table` component and all its related features, all you need to do is import the `Table` component, pass the `$inertia` object and the `users` data to the component.

```vue
<script setup>
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";

defineProps(["users"])
</script>

<template>
  <Table :inertia="$inertia" :resource="users" />
</template>
```

The `resource` property automatically detects the data and additional pagination meta data. You may also pass this manually to the component with the `data` and `meta` properties:

```vue
<template>
  <Table :inertia="$inertia" :data="users.data" :meta="users.meta" />
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
| --- | --- |
| striped | Adds a *striped* layout to the table. | `false` |
| preventOverlappingRequests | Cancels a previous visit on new user input to prevent inconsistent state | `true` |
| inputDebounceMs | Number of ms to wait before refreshing the table on user input | 350 |
| preventScroll | Configures the [Scroll preservation](https://inertiajs.com/scroll-management#scroll-preservation) behaviour. You may also pass `table-top` to this property | false |

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
| tableWrapper | The components that *wraps* the table element, handling overflow, shadow, padding, etc. |
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

#### Multiple tables per page

...

## Testing

There's a huge Laravel Dusk E2E test-suite that can be found in the `app` directory. This is a Laravel + Inertia application.

```bash
cd app
cp .env.example .env
composer install
npm install
npm run production
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan dusk:chrome-driver
php artisan serve &
php artisan dusk
```

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
