namespace {{ $namespace }}\{{ $module }}\Services\AbstractServices;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use {{ $namespace }}\{{ $module }}\Database\Models\{{ $model }};
use {{ $namespace }}\{{ $module }}\Database\Filters\{{ $model }}QueryFilter;

use {{ $namespace }}\{{ $module }}\Events\{{ Str::plural($model) }}\{{ Str::plural($model) }}CreatedEvent;
use {{ $namespace }}\{{ $module }}\Events\{{ Str::plural($model) }}\{{ Str::plural($model) }}CreatingEvent;

/**
* This class is responsible from managing the data for {{ $model }}
*
* Class {{ $model }}Service.
*
* @package {{ $namespace }}\{{ $module }}\Database\Models
*/
class Abstract{{ $model }}Service {
    public static function get({{ $model }}QueryFilter $filter = null, array $params = []) : Collection|LengthAwarePaginator {
        $enablePaginate = array_key_exists('paginate', $params);

        $perPage = config('commons.pagination.per_page');

        if($perPage == null)
            $perPage = 20;

        if(array_key_exists('per_page', $params)) {
            $perPage = intval($params['per_page']);

            if($perPage == 0)
                $perPage = 20;
        }

        if(array_key_exists('orderBy', $params)) {
            $filter->orderBy($params['orderBy']);
        }

        $model = {{ $model }}::filter($filter);

        if($model && $enablePaginate)
            return $model->paginate($perPage);
        else
            return $model->get();

        if(!$model && $enablePaginate)
            return {{ $model }}::paginate($perPage);
        else
            return {{ $model }}::get();
    }

    public static function getAll() {
        return {{ $model }}::all();
    }

    public static function create(array $data) {
        event( new {{ Str::plural($model) }}CreatingEvent() );

        $model = {{ $model }}::create([

        ]);

        event( new {{ Str::plural($model) }}CreatedEvent($model) );
    }
}