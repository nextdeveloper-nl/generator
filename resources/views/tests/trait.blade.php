namespace {{ $namespace }}\{{ $module }}\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use {{ $namespace }}\{{ $module }}\Database\Filters\{{ $model }}QueryFilter;
use {{ $namespace }}\{{ $module }}\Services\AbstractServices\Abstract{{ $model }}Service;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait {{ $model }}TestTraits
{
    public $http;

    /**
    *   Creating the Guzzle object
    */
    public function setupGuzzle()
    {
        $this->http = new Client([
            'base_uri'  =>  '127.0.0.1:8000'
        ]);
    }

    /**
    *   Destroying the Guzzle object
    */
    public function destroyGuzzle()
    {
        $this->http = null;
    }

    public function test_http_{{ strtolower($model) }}_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request('GET', '/{{ strtolower($module) }}/{{ strtolower($model) }}');

        $this->assertEquals($response->getStatusCode(), Response::HTTP_OK);
    }

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
        $result = Abstract{{ $model }}Service::get(null, [
            'paginated' =>  'true'
        ]);

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

            $filter = new {{ $model }}QueryFilter($request);

            $model = \{{ $namespace }}\{{ $module }}\Database\Models\{{ $model }}::filter($filter)->first();
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

            $filter = new {{ $model }}QueryFilter($request);

            $model = \{{ $namespace }}\{{ $module }}\Database\Models\{{ $model }}::filter($filter)->first();
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

            $filter = new {{ $model }}QueryFilter($request);

            $model = \{{ $namespace }}\{{ $module }}\Database\Models\{{ $model }}::filter($filter)->first();
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

            $filter = new {{ $model }}QueryFilter($request);

            $model = \{{ $namespace }}\{{ $module }}\Database\Models\{{ $model }}::filter($filter)->first();
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

            $filter = new {{ $model }}QueryFilter($request);

            $model = \{{ $namespace }}\{{ $module }}\Database\Models\{{ $model }}::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
@endforeach
}