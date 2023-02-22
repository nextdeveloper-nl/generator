namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use {{ $namespace }}\{{ $module }}\Tests\Database\Models\{{ $model }}TestTraits;

class GeneratedModel{{ $model }}Tests extends TestCase
{
    use {{ $model }}TestTraits;

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
