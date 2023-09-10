namespace {{ $namespace }}\{{ $module }}\Services\AbstractServices;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Commons\Helpers\DatabaseHelper;
use {{ $namespace }}\{{ $module }}\Database\Models\{{ $model }};
use {{ $namespace }}\{{ $module }}\Database\Filters\{{ $model }}QueryFilter;

use {{ $namespace }}\{{ $module }}\Events\{{ $model }}\{{ $model }}CreatedEvent;
use {{ $namespace }}\{{ $module }}\Events\{{ $model }}\{{ $model }}CreatingEvent;
use {{ $namespace }}\{{ $module }}\Events\{{ $model }}\{{ $model }}UpdatedEvent;
use {{ $namespace }}\{{ $module }}\Events\{{ $model }}\{{ $model }}UpdatingEvent;
use {{ $namespace }}\{{ $module }}\Events\{{ $model }}\{{ $model }}DeletedEvent;
use {{ $namespace }}\{{ $module }}\Events\{{ $model }}\{{ $model }}DeletingEvent;

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
        event( new {{ $model }}CreatingEvent() );

        @foreach($idFields as $field)
        if (array_key_exists('{{$field[1]}}', $data))
            $data['{{$field[1]}}'] = DatabaseHelper::uuidToId(
                '{{$field[0]}}',
                $data['{{$field[1]}}']
            );
	@endforeach
        
        try {
            $model = {{ $model }}::create($data);
        } catch(\Exception $e) {
            throw $e;
        }

        event( new {{ $model }}CreatedEvent($model) );

        return $model->fresh();
    }

/**
* This function expects the ID inside the object.
*
* @param array $data
* @return {{ $model }}
*/
public static function updateRaw(array $data) : ?{{ $model }}
{
if(array_key_exists('id', $data)) {
return self::update($data['id'], $data);
}

return null;
}

    /**
    * This method updated the model from an array.
    *
    * Throws an exception if stuck with any problem.
    *
    * @param
    * @param array $data
    * @return mixed
    * @throw Exception
    */
    public static function update($id, array $data) {
        $model = {{ $model }}::where('uuid', $id)->first();

        @foreach($idFields as $field)
        if (array_key_exists('{{$field[1]}}', $data))
            $data['{{$field[1]}}'] = DatabaseHelper::uuidToId(
                '{{$field[0]}}',
                $data['{{$field[1]}}']
            );
	@endforeach

        event( new {{ $model }}UpdatingEvent($model) );

        try {
           $isUpdated = $model->update($data);
           $model = $model->fresh();
        } catch(\Exception $e) {
           throw $e;
        }

        event( new {{ $model }}UpdatedEvent($model) );

        return $model->fresh();
    }

    /**
    * This method updated the model from an array.
    *
    * Throws an exception if stuck with any problem.
    *
    * @param
    * @param array $data
    * @return mixed
    * @throw Exception
    */
    public static function delete($id, array $data) {
        $model = {{ $model }}::where('uuid', $id)->first();

        event( new {{ $model }}DeletingEvent() );

        try {
            $model = $model->delete();
        } catch(\Exception $e) {
            throw $e;
        }

        event( new {{ $model }}DeletedEvent($model) );

        return $model;
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
