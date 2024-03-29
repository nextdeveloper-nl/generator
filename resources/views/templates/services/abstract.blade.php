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
use NextDeveloper\Commons\Exceptions\ModelNotFoundException;
use NextDeveloper\Events\Services\Events;

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
    * This method returns the sub objects of the related models
    *
    * @param $uuid
    * @param $object
    * @return void
    * @throws \Laravel\Octane\Exceptions\DdException
    */
    public static function relatedObjects($uuid, $object) {
        try {
            $obj = {{ $model }}::where('uuid', $uuid)->first();

            if(!$obj)
                throw new ModelNotFoundException('Cannot find the related model');

            if($obj)
                return $obj->$object;
        } catch (\Exception $e) {
            dd($e);
        }
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

        Events::fire('created:{{$namespace}}\{{$module}}\{{$model}}', $model);

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

        Events::fire('updated:{{$namespace}}\{{$module}}\{{$model}}', $model);

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
    public static function delete($id) {
        $model = {{ $model }}::where('uuid', $id)->first();

        Events::fire('deleted:{{$namespace}}\{{$module}}\{{$model}}', $model);

        try {
            $model = $model->delete();
        } catch(\Exception $e) {
            throw $e;
        }

        return $model;
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
