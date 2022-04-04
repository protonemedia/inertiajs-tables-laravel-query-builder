# Inertia.js Tables for Laravel Query Builder

[![Latest Version on NPM](https://img.shields.io/npm/v/@protonemedia/inertiajs-tables-laravel-query-builder.svg?style=flat-square)](https://npmjs.com/package/@protonemedia/inertiajs-tables-laravel-query-builder)
[![npm](https://img.shields.io/npm/dt/@protonemedia/inertiajs-tables-laravel-query-builder.svg?style=flat-square)](https://www.npmjs.com/package/@protonemedia/inertiajs-tables-laravel-query-builder)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/protonemedia/inertiajs-tables-laravel-query-builder.svg?style=flat-square)](https://packagist.org/packages/protonemedia/inertiajs-tables-laravel-query-builder)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![run-tests](https://github.com/protonemedia/inertiajs-tables-laravel-query-builder/workflows/run-tests/badge.svg)

This package provides a *DataTables-like* experience for [Inertia.js](https://inertiajs.com/) with support for searching, filtering, sorting, toggling columns, and pagination. It generates URLs that can be consumed by Spatie's excellent [Laravel Query Builder](https://github.com/spatie/laravel-query-builder) package, with no additional logic needed. The components are styled with [Tailwind CSS 2.0](https://tailwindcss.com/), but it's fully customizable and you can bring your own components. The data refresh logic is based on Inertia's [Ping CRM demo](https://github.com/inertiajs/pingcrm).

![Inertia.js Table for Laravel Query Builder](https://user-images.githubusercontent.com/8403149/113340981-e3863680-932c-11eb-8017-7a6588916508.mp4)

## Launcher 🚀

Hey! We've built a Docker-based deployment tool to launch apps and sites fully containerized. You can find all features and the roadmap on our [website](https://uselauncher.com), and we are on [Twitter](https://twitter.com/uselauncher) as well!

## Support

We proudly support the community by developing Laravel packages and giving them away for free. Keeping track of issues and pull requests takes time, but we're happy to help! If this package saves you time or if you're relying on it professionally, please consider [supporting the maintenance and development](https://github.com/sponsors/pascalbaljet).

## Features

* Global search
* Search per field
* Filters
* Toggle columns
* Sort columns
* Pagination
* Automatically updates the query string (by using [Inertia's replace](https://inertiajs.com/manual-visits#browser-history) feature)

## Compatibility

* [Vue 2.6](https://vuejs.org/v2/guide/installation.html) and [Vue 3](https://v3.vuejs.org/guide/installation.html)
* [Laravel 8 or 9](https://laravel.com/)
* [Inertia.js](https://inertiajs.com/)
* [Tailwind CSS v2](https://tailwindcss.com/) + [Forms plugin](https://github.com/tailwindlabs/tailwindcss-forms)
* PHP 7.4 + 8.0 + 8.1

## Roadmap

* Remove @tailwindcss/forms dependency
* Debounce delay for inputs
* Convert Table.vue styling to proper Tailwind syntax
* Improve styling on really small screens
* Better documentation about customization and move to real renderless components

## Installation

You need to install both the server-side package as well as the client-side package. Note that this package is only compatible with Laravel 8, Vue 2.6 + 3.0 and requires the Tailwind Forms plugin.

### Server-side installation (Laravel)

You can install the package via composer:

```bash
composer require protonemedia/inertiajs-tables-laravel-query-builder
```

The package will automatically register the Service Provider which provides a `table` method that you can use on an Interia Response.

#### Search fields

With the `addSearch` method, you can specify which attributes are searchable. Search queries are passed to the URL query as a `filter`. This integrates seamlessly with the [filtering feature](https://spatie.be/docs/laravel-query-builder/v3/features/filtering) of the Laravel Query Builder package.

You need to pass in the attribute and the label as arguments. With the `addSearchRows` method, you can add multiple attributes at once.

```php
Inertia::render('Page/Index')->table(function ($table) {
    $table->addSearch('name', 'Name');

    $table->addSearchRows([
        'email' => 'Email',
        'job_title' => 'Job Title',
    ]);
});
```

#### Filters

Filters are similar to search fields, but they use a `select` element instead of an `input` element. This way, you can present the user a pre-defined set of options. Under the hood, this uses the same filtering feature of the Laravel Query Builder package.

This method takes three arguments: the attribute, the label, and a key-value array with the options.

```php
Inertia::render('Page/Index')->table(function ($table) {
    $table->addFilter('language_code', 'Language', [
        'en' => 'Engels',
        'nl' => 'Nederlands',
    ]);
});
```

#### Columns

With the `addColumn` method, you can specify which columns you want to be toggleable. You need to pass in a key and label for each column. With the `addColumns` method, you can add multiple columns at once.

```php
Inertia::render('Page/Index')->table(function ($table) {
    $table->addColumn('name', 'Name');

    $table->addColumns([
        'email' => 'Email',
        'language_code' => 'Language',
    ]);
});
```

The `addColumn` method has an optional third parameter to disable the column by default:

```php
$table->addColumn('name', 'Name', false);
```

#### Disable global search

By default, global search is enabled. This query will be applied to the filters by the `global` attribute. If you don't want to use the global search, you can use the `disableGlobalSearch` method.

```php
Inertia::render('Page/Index')->table(function ($table) {
    $table->disableGlobalSearch();
});
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
                $query->where('name', 'LIKE', "%{$value}%")->orWhere('email', 'LIKE', "%{$value}%");
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
            $table->addSearchRows([
                'name' => 'Name',
                'email' => 'Email address',
            ])->addFilter('language_code', 'Language', [
                'en' => 'Engels',
                'nl' => 'Nederlands',
            ])->addColumns([
                'email' => 'Email address',
                'language_code' => 'Language',
            ]);
        });
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

To use the `Table` component and all its related features, you need to add the `InteractsWithQueryBuilder` mixin to your component and add the `Tailwind2.Table` component to the `components` key.

You can use the named `#head` slot to provide the table header and the named `#body` slot to provide the table body. You can use the `showColumn` method to determine if a column should be visible or not. You can use the `sortBy` method to set the column you want to sort by.

#### Page component example

```vue
<template>
  <Table
    :filters="queryBuilderProps.filters"
    :search="queryBuilderProps.search"
    :columns="queryBuilderProps.columns"
    :on-update="setQueryBuilder"
    :meta="users"
  >
    <template #head>
      <tr>
        <th @click.prevent="sortBy('name')">Name</th>
        <th v-show="showColumn('email')" @click.prevent="sortBy('email')">Email</th>
        <th v-show="showColumn('language_code')" @click.prevent="sortBy('language_code')">Language</th>
      </tr>
    </template>

    <template #body>
      <tr v-for="user in users.data" :key="user.id">
        <td>{{ user.name }}</td>
        <td v-show="showColumn('email')">{{ user.email }}</td>
        <td v-show="showColumn('language_code')">{{ user.language_code }}</td>
      </tr>
    </template>
  </Table>
</template>

<script>
import { InteractsWithQueryBuilder, Tailwind2 } from '@protonemedia/inertiajs-tables-laravel-query-builder';

export default {
  mixins: [InteractsWithQueryBuilder],

  components: {
    Table: Tailwind2.Table
  },

  props: {
    users: Object
  }
};
</script>
```

#### Attributes and pagination

The `filters`, `search`, `columns`, and `on-update` attributes of the `Table` component are required, but the the `InteractsWithQueryBuilder` mixin magically provides the values for those attributes. You just have to specify them like the example template above.

When you pass a `meta` object to the table, it will automatically provide a pagination component.

You can override the default pagination translations with the `setTranslations` method of the base component. You can do this in your main JavaScript file:

```js
import { Components } from "@protonemedia/inertiajs-tables-laravel-query-builder";

Components.Pagination.setTranslations({
  no_results_found: "No results found",
  previous: "Previous",
  next: "Next",
  to: "to",
  of: "of",
  results: "results",
});
```

#### Table.vue slots

The `Table.vue` has several slots that you can use to inject your own implementations.

| Slot | Description |
| --- | --- |
| tableFilter | The location of the button + dropdown to select filters. |
| tableGlobalSearch | The location of the input element that handles the global search. |
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
  <Table ...>
    <template v-slot:tableGlobalSearch="slotProps">
      <input
        placeholder="Custom Global Search Component..."
        :value="slotProps.search.global.value"
        @input="slotProps.changeGlobalSearchValue($event.target.value)"
      />
    </template>

    <template #body>
      ...
    </template>
  </Table>
</template>
```

#### Bring your own components

The templates and logic of the components are entirely separated. This way, you can create new templates while reusing the existing logic.

There are nine components that you can import and use as a mixin for your templates. For example, to write your own `TableGlobalSearch` component, you can import the base component and use its logic by adding it as a mixin.

```vue
<template>
  <input
    class="form-input"
    placeholder="Custom Global Search Component..."
    :value="value"
    @input="onChange($event.target.value)"
  />
</template>

<script>
import { Components } from '@protonemedia/inertiajs-tables-laravel-query-builder';

export default {
  mixins: [Components.TableGlobalSearch],
};
</script>
```

Available components:
* Components.ButtonWithDropdown
* Components.OnClickOutside
* Components.Pagination
* Components.Table
* Components.TableAddSearchRow
* Components.TableColumns
* Components.TableFilter
* Components.TableGlobalSearch
* Components.TableSearchRows

A good starting point would be to duplicate the `js/Tailwind2` folder into your app and start customizing the templates from there.

## Testing

You can run the PHP test suite with `composer`:

```bash
composer test
```

You can run the JS test suite with either `npm` or `yarn`:

```bash
npm run test

yarn test
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
