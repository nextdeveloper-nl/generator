namespace {{ $namespace }}\{{ $module }}\Services\AbstractServices;

use Illuminate\Http\Request;
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

        /**
        * Here we are adding null request since if filter is null, this means that this function is called from
        * non http application. This is actually not I think its a correct way to handle this problem but it's a workaround.
        *
        * Please let me know if you have any other idea about this; baris.bulut@nextdeveloper.com
        */
        if($filter == null)
            $filter = new {{ $model }}QueryFilter(new Request());

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

    /**
    * This method returns the model by looking at reference id
    *
    * @param $ref
    * @return mixed
    */
    public static function getByRef($ref) : ?{{ $model }} {
        return {{ $model }}::findByRef($ref);
    }

    /**
    * This method returns the model by lookint at its id
    *
    * @param $id
    * @return {{ $model }}|null
    */
    public static function getById($id) : ?{{ $model }} {
        return {{ $model }}::where('id', $id)->first();
    }

    /**
    * This method created the model from an array.
    *
    * Throws an exception if stuck with any problem.
    *
    * @param array $data
    * @return mixed
    * @throw Exception
    */
    public static function create(array $data) {
        event( new {{ Str::plural($model) }}CreatingEvent() );

        try {
            $model = {{ $model }}::create([

            ]);
        } catch(\Exception $e) {
            throw $e;
        }

        event( new {{ Str::plural($model) }}CreatedEvent($model) );

        return $model;
    }
}