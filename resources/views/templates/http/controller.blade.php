namespace {{ $namespace }}\{{ $module }}\Http\Controllers\{{ $model }};

use Illuminate\Http\Request;
use NextDeveloper\Generator\Common\AbstractController;
use NextDeveloper\Generator\Http\Traits\ResponsableFactory;
use {{ $namespace }}\{{ $module }}\Http\Requests\{{ $model }}\{{ $model }}UpdateRequest;
use {{ $namespace }}\{{ $module }}\Database\Filters\{{ $model }}QueryFilter;
use {{ $namespace }}\{{ $module }}\Services\{{ $model }}Service;
use {{ $namespace }}\{{ $module }}\Http\Requests\{{ $model }}\{{ $model }}CreateRequest;

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

    /**
    * This method receives ID for the related model and returns the item to the client.
    *
    * @param ${{ Str::camel($model) }}Id
    * @return mixed|null
    * @throws \Laravel\Octane\Exceptions\DdException
    */
    public function show($ref) {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file in NextDeveloper Platform Project
        $model = {{ $model }}Service::getByRef($ref);

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
    * This method created {{ $model }} object on database.
    *
    * @param CountryCreateRequest $request
    * @return mixed|null
    * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
    */
    public function store(CountryCreateRequest $request) {
        $model = CountryService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
    * This method updates {{ $model }} object on database.
    *
    * @param ${{ Str::camel($model) }}Id
    * @param CountryCreateRequest $request
    * @return mixed|null
    * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
    */
    public function update(${{ Str::camel($model) }}Id, CountryUpdateRequest $request) {
        $model = CountryService::update($countryId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }
}