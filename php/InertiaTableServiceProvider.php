<?php

namespace ProtoneMedia\LaravelQueryBuilderInertiaJs;

use Illuminate\Support\ServiceProvider;
use Inertia\Response;

class InertiaTableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Response::macro('table', function (callable $withTableBuilder = null) {
            $tableBuilder = new InertiaTable(request());

            if ($withTableBuilder) {
                $withTableBuilder($tableBuilder);
            }

            return $tableBuilder->applyTo($this);
        });
    }
}
