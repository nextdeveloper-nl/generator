
namespace {{ $namespace }}\{{ $module }}\EventHandlers\{{ $model}};

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class {{ $handler }}
 * @package PlusClouds\Account\Handlers\Events
 */
class {{ $handler }} implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event)
    {

    }
}
