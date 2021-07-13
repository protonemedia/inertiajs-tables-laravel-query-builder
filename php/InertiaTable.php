<?php

namespace ProtoneMedia\LaravelQueryBuilderInertiaJs;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Inertia\Response;

class InertiaTable
{
    private Request $request;
    private Collection $columns;
    private Collection $search;
    private Collection $filters;
    private bool $globalSearch = true;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->columns = new Collection;
        $this->search  = new Collection;
        $this->filters = new Collection;
    }

    /**
     * Disable the global search.
     *
     * @return self
     */
    public function disableGlobalSearch(): self
    {
        $this->globalSearch = false;

        return $this;
    }

    /**
     * Collects all properties and sets the default
     * values from the request query.
     *
     * @return array
     */
    public function getQueryBuilderProps(): array
    {
        $columns = $this->transformColumns();
        $search  = $this->transformSearch();
        $filters = $this->transformFilters();

        return [
            'sort'    => $this->request->query('sort'),
            'page'    => Paginator::resolveCurrentPage(),
            'columns' => $columns->isNotEmpty() ? $columns->all() : (object) [],
            'search'  => $search->isNotEmpty() ? $search->all() : (object) [],
            'filters' => $filters->isNotEmpty() ? $filters->all() : (object) [],
        ];
    }

    /**
     * Transform the columns collection so it can be used in the Inertia front-end.
     *
     * @return \Illuminate\Support\Collection
     */
    private function transformColumns(): Collection
    {
        $columns = $this->request->query('columns', []);

        if (empty($columns)) {
            return $this->columns;
        }

        return $this->columns->map(function ($column, $key) use ($columns) {
            if (!in_array($key, $columns)) {
                $column['enabled'] = false;
            }

            return $column;
        });
    }

    /**
     * Transform the search collection so it can be used in the Inertia front-end.
     *
     * @return \Illuminate\Support\Collection
     */
    private function transformSearch(): Collection
    {
        $search = $this->search->collect();

        if ($this->globalSearch) {
            $search->prepend([
                'key'   => 'global',
                'label' => 'global',
                'value' => null,
            ], 'global');
        }

        $filters = $this->request->query('filter', []);

        if (empty($filters)) {
            return $search;
        }

        return $search->map(function ($search, $key) use ($filters) {
            if (!array_key_exists($key, $filters)) {
                return $search;
            }

            $search['value'] = $filters[$key];
            $search['enabled'] = true;

            return $search;
        });
    }

    /**
     * Transform the filters collection so it can be used in the Inertia front-end.
     *
     * @return \Illuminate\Support\Collection
     */
    private function transformFilters(): Collection
    {
        $filters = $this->request->query('filter', []);

        if (empty($filters)) {
            return $this->filters;
        }

        return $this->filters->map(function ($filter, $key) use ($filters) {
            if (!array_key_exists($key, $filters)) {
                return $filter;
            }

            $value = $filters[$key];

            if (!array_key_exists($value, $filter['options'] ?? [])) {
                return $filter;
            }

            $filter['value'] = $value;

            return $filter;
        });
    }

    /**
     * Give the query builder props to the given Inertia response.
     *
     * @param \Inertia\Response $response
     * @return \Inertia\Response
     */
    public function applyTo(Response $response): Response
    {
        return $response->with('queryBuilderProps', $this->getQueryBuilderProps());
    }

    /**
     * Add a column to the query builder.
     *
     * @param string $key
     * @param string $label
     * @param bool $enabled
     * @return self
     */
    public function addColumn(string $key, string $label, bool $enabled = true): self
    {
        $this->columns->put($key, [
            'key'     => $key,
            'label'   => $label,
            'enabled' => $enabled,
        ]);

        return $this;
    }

    public function addColumns(array $columns = []): self
    {
        foreach ($columns as $key => $value) {
            $this->addColumn($key, $value, true);
        }

        return $this;
    }

    /**
     * Add a search row to the query builder.
     *
     * @param string $key
     * @param string $label
     * @return self
     */
    public function addSearch(string $key, string $label): self
    {
        $this->search->put($key, [
            'key'   => $key,
            'label' => $label,
            'value' => null,
        ]);

        return $this;
    }

    public function addSearchRows(array $columns = []): self
    {
        foreach ($columns as $key => $label) {
            $this->addSearch($key, $label);
        }

        return $this;
    }

    /**
     * Add a filter to the query builder.
     *
     * @param string $key
     * @param string $label
     * @param array $options
     * @return self
     */
    public function addFilter(string $key, string $label, array $options): self
    {
        $this->filters->put($key, [
            'key'     => $key,
            'label'   => $label,
            'options' => Arr::prepend($options, '-', ''),
            'value'   => null,
        ]);

        return $this;
    }
}
