namespace {{ $namespace }}\{{ $module }}\Http\Controllers\{{ $model }};

use Illuminate\Http\Request;
use NextDeveloper\Generator\Common\AbstractController;
use NextDeveloper\Generator\Http\Traits\ResponsableFactory;
use {{ $namespace }}\{{ $module }}\Database\Filters\{{ $model }}QueryFilter;
use {{ $namespace }}\{{ $module }}\Http\Transformers\{{ $model }}Transformer;
use {{ $namespace }}\{{ $module }}\Services\{{ $model }}Service;

class {{ $model }}Controller extends AbstractController
{
    /**
    * This method returns the list of {{ str::plural(strtolower($model)) }}.
    *
    * optional http params:
    * - paginate: If you set paginate parameter, the result will be returned paginated.
    *
    * @param {{ $model }}QueryFilter $filter An object that builds search query
    * @param Request $request Laravel request object, this holds all data about request. Automatically populated.
    * @return \Illuminate\Http\JsonResponse
    */
    public function index({{ $model }}QueryFilter $filter, Request $request) {
        $data = {{ $model }}Service::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }
}