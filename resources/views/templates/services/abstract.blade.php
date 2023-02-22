namespace {{ $namespace }}\{{ $module }}\Services\AbstractServices;

use {{ $namespace }}\{{ $module }}\Database\Models\{{ $model }};
use {{ $namespace }}\{{ $module }}\Database\Filters\{{ $model }}Filter;

/**
* This class is responsible from managing the data for {{ $model }}
*
* Class {{ $model }}Service.
*
* @package {{ $namespace }}\{{ $module }}\Database\Models
*/
class Abstract{{ $model }}Service {
    public static function get({{ $model }}Filter $filter, bool $enablePaginate = true, $page = 0) : ?{{ $model }} {
        $model = {{ $model }}::filter($filter)

        if($enablePaginate)
            $data = $model->paginate();
        else
            $data = $model->get();

        return $data;
    }

    public static function getAll({{ $model }}Filter $filter) {
        return {{ $model }}::filter($filter)->get();
    }

    public static function create($data) {

    }
}