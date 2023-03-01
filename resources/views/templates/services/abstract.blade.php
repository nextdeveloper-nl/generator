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
    public static function get({{ $model }}Filter $filter = null, bool $enablePaginate = true, $page = 0) : ?Collection {
        if($filter)
            return {{ $model }}::filter($filter)->get();
        else
            return {{ $model }}::get();
    }

    public static function getPaginated({{ $model }}Filter $filter = null, bool $enablePaginate = true, $page = 0) : ?LengthAwarePaginator {
        if($filter)
            return {{ $model }}::filter($filter)->paginate();
        else
            return {{ $model }}::paginate();
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