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
    public function test_get()
    {
        $result = Abstract{{ $model }}Service::get();

        $this->assertIsObject($result, Collection::class);
    }
}
