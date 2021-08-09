<?php

namespace ProtoneMedia\LaravelQueryBuilderInertiaJs\Tests;

use Illuminate\Http\Request;
use Illuminate\Testing\Assert;
use Orchestra\Testbench\TestCase;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;

class InertiaTableTest extends TestCase
{
    private function request(callable $callback = null): Request
    {
        $request = Request::createFromGlobals();

        return $callback ? tap($request, $callback) : $request;
    }

    /** @test */
    public function it_gets_the_sort_from_the_request_query()
    {
        $request = $this->request(function (Request $request) {
            $request->query->set('sort', 'name');
        });

        $props = (new InertiaTable($request))->getQueryBuilderProps();

        $this->assertEquals("name", $props['sort']);
    }

    /** @test */
    public function it_can_add_a_column_to_toggle()
    {
        $table = new InertiaTable($this->request());
        $table->addColumn('name', 'Name');

        $props = $table->getQueryBuilderProps();

        Assert::assertArraySubset([
            "columns" => [
                "name" => [
                    "key"     => "name",
                    "label"   => "Name",
                    "enabled" => true,
                ],
            ],
        ], $props);
    }

    /** @test */
    public function it_can_add_a_column_that_is_disabled_by_default()
    {
        $table = new InertiaTable($this->request());
        $table->addColumn('name', 'Name', false);

        $props = $table->getQueryBuilderProps();

        Assert::assertArraySubset([
            "columns" => [
                "name" => [
                    "key"     => "name",
                    "label"   => "Name",
                    "enabled" => false,
                ],
            ],
        ], $props);
    }

    /** @test */
    public function it_gets_the_default_toggled_columns_from_the_query_String()
    {
        $table = new InertiaTable($this->request(function (Request $request) {
            $request->query->set('columns', ['name', 'country']);
        }));

        $table->addColumns([
            'name'    => 'Name',
            'email'   => 'Email',
            'country' => 'Country',
        ]);

        $props = $table->getQueryBuilderProps();

        $this->assertTrue($props['columns']['name']['enabled']);
        $this->assertFalse($props['columns']['email']['enabled']);
        $this->assertTrue($props['columns']['country']['enabled']);
    }

    /** @test */
    public function it_can_add_a_search_row()
    {
        $table = new InertiaTable($this->request());
        $table->addSearch('name', 'Name');

        $props = $table->getQueryBuilderProps();

        Assert::assertArraySubset([
            "search" => [
                "name" => [
                    "key"   => "name",
                    "label" => "Name",
                    "value" => null,
                ],
            ],
        ], $props);
    }

    /** @test */
    public function it_gets_the_default_search_values_from_the_request_query()
    {
        $table = new InertiaTable($this->request(function (Request $request) {
            $request->query->set('filter', [
                'name'  => 'pascal',
                'email' => '@protone.media',
            ]);
        }));

        $table->addSearchRows([
            'name'    => 'Name',
            'email'   => 'Email',
            'country' => 'Country',
        ]);

        $props = $table->getQueryBuilderProps();

        $this->assertEquals('pascal', $props['search']['name']['value']);
        $this->assertEquals('@protone.media', $props['search']['email']['value']);
        $this->assertNull($props['search']['country']['value']);
    }

    /** @test */
    public function it_gets_the_default_filter_values_from_the_request_query()
    {
        $table = new InertiaTable($this->request(function (Request $request) {
            $request->query->set('filter', [
                'name'    => 'a',
                'email'   => 'b',
                'country' => 'c',
            ]);
        }));

        $table->addFilter('name', 'Name', ['a' => 'Option A'])
            ->addFilter('email', 'Email', ['a' => 'Option A', 'b' => 'Option B'])
            ->addFilter('country', 'Country', []);

        $props = $table->getQueryBuilderProps();

        $this->assertEquals('a', $props['filters']['name']['value']);
        $this->assertEquals('b', $props['filters']['email']['value']);
        $this->assertNull($props['filters']['country']['value']);
    }

    /** @test */
    public function it_can_add_a_filter_with_options()
    {
        $table = new InertiaTable($this->request());
        $table->addFilter('name', 'Name', $options = [
            'a' => 'Option A',
            'b' => 'Option B',
        ]);

        $props = $table->getQueryBuilderProps();

        Assert::assertArraySubset([
            "filters" => [
                "name" => [
                    "key"     => "name",
                    "label"   => "Name",
                    "value"   => null,
                    "options" => [
                        ''  => '-',
                        'a' => 'Option A',
                        'b' => 'Option B',
                    ],
                ],
            ],
        ], $props);
    }
}
