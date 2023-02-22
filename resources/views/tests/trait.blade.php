namespace {{ $namespace }}\{{ $module }}\Tests\Database\Models;

use PHPUnit\Framework\TestCase;
use {{ $namespace }}\{{ $module }}\Services\AbstractServices\Abstract{{ $model }}Service;

trait {{ $model }}TestTraits
{
    /**
    * A basic unit test example.
    *
    * @return void
    */
    public function test_example()
    {
        $this->assertTrue(true);
    }
}
