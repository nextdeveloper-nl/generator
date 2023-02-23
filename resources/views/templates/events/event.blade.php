
namespace {{ $namespace }}\{{ $module }}\Events\{{ $model }}s;

use Illuminate\Queue\SerializesModels;

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
}