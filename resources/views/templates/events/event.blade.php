
namespace {{ $namespace }}\{{ $module }}\Events\{{ $model }};

use Illuminate\Queue\SerializesModels;
use {{ $namespace }}\{{ $module }}\Database\Models\{{ $model }};

/**
 * Class {{ $event }}
 * @package {{ $namespace }}\{{ $module }}\Events
 */
class {{ $event }}
{
    use SerializesModels;

    /**
     * @var {{ $model }}
     */
    public $_model;

    /**
     * @var int|null
     */
    protected $timestamp = null;

    public function __construct({{ $model }} $model = null) {
        $this->_model = $model;
    }

    /**
    * @param int $value
    *
    * @return AbstractEvent
    */
    public function setTimestamp($value) {
        $this->timestamp = $value;

        return $this;
    }

    /**
    * @return int|null
    */
    public function getTimestamp() {
        return $this->timestamp;
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}