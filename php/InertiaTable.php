<?php

namespace ProtoneMedia\LaravelQueryBuilderInertiaJs;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inertia\Response;

class InertiaTable
{
    private string $name = 'default';
    private Request $request;
    private Collection $columns;
    private Collection $searchInputs;
    private Collection $filters;
    private string $defaultSort = '';

    public function __construct(Request $request)
    {
        $this->request      = $request;
        $this->columns      = new Collection;
        $this->searchInputs = new Collection;
        $this->filters      = new Collection;
    }

    /**
     * Name for this table.
     *
     * @param string $name
     * @return self
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Default sort for this table.
     *
     * @param string $defaultSort
     * @return self
     */
    public function defaultSort(string $defaultSort): self
    {
        $this->defaultSort = $defaultSort;

        return $this;
    }

    /**
     * Collects all properties and sets the default
     * values from the request query.
     *
     * @return array
     */
    protected function getQueryBuilderProps(): array
    {
        return [
            'defaultVisibleToggleableColumns' => $this->columns->reject->hidden->map->key->sort()->values(),
            'columns'                         => $this->transformColumns(),
            'hasHiddenColumns'                => $this->columns->filter->hidden->isNotEmpty(),
            'hasToggleableColumns'            => $this->columns->filter->canBeHidden->isNotEmpty(),

            'filters'           => $this->transformFilters(),
            'hasFilters'        => $this->filters->isNotEmpty(),
            'hasEnabledFilters' => $this->filters->filter->value->isNotEmpty(),

            'searchInputs'                => $searchInputs = $this->transformSearchInputs(),
            'searchInputsWithoutGlobal'   => $searchInputsWithoutGlobal = $searchInputs->where('key', '!=', 'global'),
            'hasSearchInputs'             => $searchInputsWithoutGlobal->isNotEmpty(),
            'hasSearchInputsWithValue'    => $searchInputsWithoutGlobal->whereNotNull('value')->isNotEmpty(),
            'hasSearchInputsWithoutValue' => $searchInputsWithoutGlobal->whereNull('value')->isNotEmpty(),

            'globalSearch' => $this->searchInputs->firstWhere('key', 'global'),

            'cursor' => $this->request->query('cursor'),
            'sort'   => $this->request->query('sort', $this->defaultSort) ?: null,
            'page'   => Paginator::resolveCurrentPage(),
        ];
    }

    /**
     * Transform the columns collection so it can be used in the Inertia front-end.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function transformColumns(): Collection
    {
        $columns = $this->request->query('columns', []);

        $sort = $this->request->query('sort', $this->defaultSort);

        return $this->columns->map(function (Column $column) use ($columns, $sort) {
            $key = $column->key;

            if (! empty($columns)) {
                $column->hidden = ! in_array($key, $columns);
            }

            if ($sort === $key) {
                $column->sorted = 'asc';
            } elseif ($sort === "-{$key}") {
                $column->sorted = 'desc';
            }

            return $column;
        });
    }

    /**
     * Transform the search collection so it can be used in the Inertia front-end.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function transformFilters(): Collection
    {
        $filters = $this->filters;

        $queryFilters = $this->request->query('filter', []);

        if (empty($queryFilters)) {
            return $filters;
        }

        return $filters->map(function (Filter $filter) use ($queryFilters) {
            if (array_key_exists($filter->key, $queryFilters)) {
                $filter->value = $queryFilters[$filter->key];
            }

            return $filter;
        });
    }

    /**
     * Transform the filters collection so it can be used in the Inertia front-end.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function transformSearchInputs(): Collection
    {
        $filters = $this->request->query('filter', []);

        if (empty($filters)) {
            return $this->searchInputs;
        }

        return $this->searchInputs->map(function (SearchInput $searchInput) use ($filters) {
            if (array_key_exists($searchInput->key, $filters)) {
                $searchInput->value = $filters[$searchInput->key];
            }

            return $searchInput;
        });
    }

    /**
     * Add a column to the query builder.
     *
     * @return self
     */
    public function column(string $key = null, string $label = null, bool $canBeHidden = true, bool $hidden = false, bool $sortable = false, bool $searchable = false, bool $custom = false): self
    {
        $this->columns->push($column = new Column(
            key: $key ?: Str::kebab($label),
            label: $label ?: Str::headline($key),
            canBeHidden: $canBeHidden,
            hidden: $hidden,
            sortable: $sortable,
            sorted: false
        ));

        if ($searchable) {
            $this->searchInput($column->key, $column->label);
        }

        return $this;
    }

    public function withGlobalSearch(string $label = null): self
    {
        return $this->searchInput('global', $label ?: __('Search...'));
    }

    public function searchInput(string $key, string $label = null, string $defaultValue = null): self
    {
        $this->searchInputs->push(new SearchInput(
            key: $key,
            label: $label ?: Str::headline($key),
            value: $defaultValue
        ));

        return $this;
    }

    /**
     * Add a select filter to the query builder.
     *
     * @param string $key
     * @param string $label
     * @param array $options
     * @return self
     */
    public function selectFilter(string $key, string $label = null, array $options, string $defaultValue = null, bool $noFilterOption = true, string $noFilterOptionLabel = null): self
    {
        $this->filters->push(new Filter(
            key: $key,
            label: $label ?: Str::headline($key),
            options: $options,
            value: $defaultValue,
            noFilterOption: $noFilterOption,
            noFilterOptionLabel: $noFilterOptionLabel ?: '-',
            type: 'select'
        ));

        return $this;
    }

    /**
     * Give the query builder props to the given Inertia response.
     *
     * @param \Inertia\Response $response
     * @return \Inertia\Response
     */
    public function applyTo(Response $response): Response
    {
        if (! Response::hasMacro('getQueryBuilderProps')) {
            Response::macro('getQueryBuilderProps', function () {
                return $this->props['queryBuilderProps'] ?? [];
            });
        }

        $props = array_merge($response->getQueryBuilderProps(), [
            $this->name => $this->getQueryBuilderProps(),
        ]);

        return $response->with('queryBuilderProps', $props);
    }
}
