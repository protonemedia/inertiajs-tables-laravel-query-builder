<?php

namespace ProtoneMedia\LaravelQueryBuilderInertiaJs;

use Illuminate\Support\ServiceProvider;
use Inertia\Response as InertiaResponse;

class InertiaTableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        InertiaResponse::macro('getQueryBuilderProps', function () {
            return $this->props['queryBuilderProps'] ?? [];
        });

        InertiaResponse::macro('table', function (callable $withTableBuilder = null) {
            $tableBuilder = new InertiaTable(request());

            if ($withTableBuilder) {
                $withTableBuilder($tableBuilder);
            }

            return $tableBuilder->applyTo($this);
        });
    }
}
