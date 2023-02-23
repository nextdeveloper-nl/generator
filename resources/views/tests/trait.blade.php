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
}
