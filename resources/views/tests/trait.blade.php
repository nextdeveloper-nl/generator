namespace {{ $namespace }}\{{ $module }}\Tests\Database\Models;

use Tests\TestCase;
use {{ $namespace }}\{{ $module }}\Services\AbstractServices\Abstract{{ $model }}Service;

trait {{ $model }}TestTraits
{
    /**
    * Get test
    *
    * @return bool
    */
    public function test_{{ $model }}_model_get()
    {
        $result = Abstract{{ $model }}Service::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_{{ $model }}_get_all()
    {
        $result = Abstract{{ $model }}Service::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_{{ $model }}_get_paginated()
    {
        $result = Abstract{{ $model }}Service::getPaginated();

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

@foreach( $events as $event )
    public function test_{{ $model }}_event_{{ $event }}_without_object()
    {
        @php
        $modelName = Str::camel($model);
        $modelName = Str::ucfirst($modelName);
        $modelName = Str::plural($modelName);

        $event = $modelName . '' . Str::ucfirst($event) . 'Event';
        @endphp
try {
            event( new \{{ $namespace }}\{{ $module }}\Events\{{ str::plural($modelName) }}\{{ $event }}() );
        } catch (\Exception $e) {
            $this->assertFalse();
        }

        $this->assertTrue(true);
    }
@endforeach

@foreach( $events as $event )
    public function test_{{ $model }}_event_{{ $event }}_with_object()
    {
    @php
        $modelName = Str::camel($model);
        $modelName = Str::ucfirst($modelName);
        $modelName = Str::plural($modelName);

        $event = $modelName . '' . Str::ucfirst($event) . 'Event';
    @endphp
    try {
    $model = \{{ $namespace }}\{{ $module }}\Database\Models\{{ $model }}::first();

    event( new \{{ $namespace }}\{{ $module }}\Events\{{ str::plural($modelName) }}\{{ $event }}($model) );
    } catch (\Exception $e) {
    $this->assertFalse();
    }

    $this->assertTrue(true);
    }
@endforeach
}