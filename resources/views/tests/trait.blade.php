namespace {{ $namespace }}\{{ $module }}\Tests\Database\Models;

use Tests\TestCase;
use Illuminate\Http\Request;
use {{ $namespace }}\{{ $module }}\Database\Filters\{{ $model }}QueryFilter;
use {{ $namespace }}\{{ $module }}\Services\AbstractServices\Abstract{{ $model }}Service;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait {{ $model }}TestTraits
{
    /**
    * Get test
    *
    * @return bool
    */
    public function test_{{ strtolower($model) }}_model_get()
    {
        $result = Abstract{{ $model }}Service::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_{{ strtolower($model) }}_get_all()
    {
        $result = Abstract{{ $model }}Service::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_{{ strtolower($model) }}_get_paginated()
    {
        $result = Abstract{{ $model }}Service::getPaginated();

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

@foreach( $events as $event )
    public function test_{{ strtolower($model) }}_event_{{ $event }}_without_object()
    {
        @php
        $modelName = Str::camel($model);
        $modelName = Str::ucfirst($modelName);
        $modelName = Str::plural($modelName);

        $event = $modelName . '' . Str::ucfirst($event) . 'Event';
        @endphp
try {
            event( new \{{ $namespace }}\{{ $module }}\Events\{{ Str::plural($modelName) }}\{{ $event }}() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
@endforeach

@foreach( $events as $event )
    public function test_{{ strtolower($model) }}_event_{{ $event }}_with_object()
    {
    @php
        $modelName = Str::camel($model);
        $modelName = Str::ucfirst($modelName);
        $modelName = Str::plural($modelName);

        $event = $modelName . '' . Str::ucfirst($event) . 'Event';
    @endphp
    try {
            $model = \{{ $namespace }}\{{ $module }}\Database\Models\{{ $model }}::first();

            event( new \{{ $namespace }}\{{ $module }}\Events\{{ Str::plural($modelName) }}\{{ $event }}($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
@endforeach
@foreach( $filterTextFields as $field )

    public function test_{{ strtolower($model) }}_event_{{ $field }}_filter()
    {
    @php
        $modelName = Str::camel($model);
        $modelName = Str::ucfirst($modelName);
        $modelName = Str::plural($modelName);

        $event = $modelName . '' . Str::ucfirst($event) . 'Event';
    @endphp
    try {
            $request = new Request([
                '{{ str_replace('-', '_', Str::kebab($field)) }}'  =>  'a'
            ]);

            $filter = new CountryQueryFilter($request);

            $model = \NextDeveloper\Commons\Database\Models\Country::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
@endforeach
@foreach( $filterNumberFields as $field )

    public function test_{{ strtolower($model) }}_event_{{ $field }}_filter()
    {
    @php
        $modelName = Str::camel($model);
        $modelName = Str::ucfirst($modelName);
        $modelName = Str::plural($modelName);

        $event = $modelName . '' . Str::ucfirst($event) . 'Event';
    @endphp
    try {
            $request = new Request([
                '{{ str_replace('-', '_', Str::kebab($field)) }}'  =>  '1'
            ]);

            $filter = new CountryQueryFilter($request);

            $model = \NextDeveloper\Commons\Database\Models\Country::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
@endforeach
@foreach( $filterDateFields as $field )

    public function test_{{ strtolower($model) }}_event_{{ $field }}_filter_start()
    {
    @php
        $modelName = Str::camel($model);
        $modelName = Str::ucfirst($modelName);
        $modelName = Str::plural($modelName);

        $event = $modelName . '' . Str::ucfirst($event) . 'Event';
    @endphp
    try {
            $request = new Request([
                '{{ str_replace('-', '_', Str::kebab($field)) }}Start'  =>  now()
            ]);

            $filter = new CountryQueryFilter($request);

            $model = \NextDeveloper\Commons\Database\Models\Country::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
@endforeach
@foreach( $filterDateFields as $field )

    public function test_{{ strtolower($model) }}_event_{{ $field }}_filter_end()
    {
    @php
        $modelName = Str::camel($model);
        $modelName = Str::ucfirst($modelName);
        $modelName = Str::plural($modelName);

        $event = $modelName . '' . Str::ucfirst($event) . 'Event';
    @endphp
    try {
            $request = new Request([
                '{{ str_replace('-', '_', Str::kebab($field)) }}End'  =>  now()
            ]);

            $filter = new CountryQueryFilter($request);

            $model = \NextDeveloper\Commons\Database\Models\Country::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
@endforeach
@foreach( $filterDateFields as $field )

    public function test_{{ strtolower($model) }}_event_{{ $field }}_filter_start_and_end()
    {
    @php
        $modelName = Str::camel($model);
        $modelName = Str::ucfirst($modelName);
        $modelName = Str::plural($modelName);

        $event = $modelName . '' . Str::ucfirst($event) . 'Event';
    @endphp
    try {
            $request = new Request([
                '{{ str_replace('-', '_', Str::kebab($field)) }}Start'  =>  now(),
                '{{ str_replace('-', '_', Str::kebab($field)) }}End'  =>  now()
            ]);

            $filter = new CountryQueryFilter($request);

            $model = \NextDeveloper\Commons\Database\Models\Country::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
@endforeach
}