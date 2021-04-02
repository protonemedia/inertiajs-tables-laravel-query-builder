<?php

namespace ProtoneMedia\LaravelQueryBuilderInertiaJs;

use Illuminate\Support\ServiceProvider;
use Inertia\Response;

class InertiaTableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Response::macro('table', function (callable $withTableBuilder = null) {
            $request = request();

            $response = $this;

            if ($withTableBuilder) {
                $tableBuilder = new InertiaTable($request);
                $withTableBuilder($tableBuilder);
                $tableBuilder->applyTo($response);
            }

            return $response;
        });
    }
}
