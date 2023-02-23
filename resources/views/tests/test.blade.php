namespace Tests\Unit;

use Tests\TestCase;
use {{ $namespace }}\{{ $module }}\Tests\Database\Models\{{ $model }}TestTraits;

class GeneratedModel{{ $model }}Test extends TestCase
{
    use {{ $model }}TestTraits;
}
