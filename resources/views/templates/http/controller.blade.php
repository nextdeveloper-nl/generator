namespace {{ $namespace }}\{{ $module }}\Http\Controllers\{{ $model }};

use Illuminate\Http\Request;
use {{ $namespace }}\{{ $module }}\Http\Controllers\AbstractController;
use NextDeveloper\Generator\Http\Traits\ResponsableFactory;
use {{ $namespace }}\{{ $module }}\Http\Requests\{{ $model }}\{{ $model }}UpdateRequest;
use {{ $namespace }}\{{ $module }}\Database\Filters\{{ $model }}QueryFilter;
use {{ $namespace }}\{{ $module }}\Database\Models\{{ $model }};
use {{ $namespace }}\{{ $module }}\Services\{{ $model }}Service;
use {{ $namespace }}\{{ $module }}\Http\Requests\{{ $model }}\{{ $model }}CreateRequest;
@php
if($traits) {
    foreach ($traits as $trait) {
        echo 'use ' . $trait['class'] . ';';
    }
}
@endphp

class {{ $model }}Controller extends AbstractController
{
    private $model = {{ $model }}::class;

@php
    if($traits) {
        foreach ($traits as $trait) {
            echo 'use ' . $trait['name'] . ';' . PHP_EOL;
        }
    }
@endphp
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
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = {{ $model }}Service::getByRef($ref);

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
    * This method returns the list of sub objects the related object. Sub object means an object which is preowned by
    * this object.
    *
    * It can be tags, addresses, states etc.
    *
    * @param $ref
    * @param $subObject
    * @return void
    */
    public function relatedObjects($ref, $subObject) {
        $objects = {{ $model }}Service::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
    * This method created {{ $model }} object on database.
    *
    * @param {{ $model }}CreateRequest $request
    * @return mixed|null
    * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
    */
    public function store({{ $model }}CreateRequest $request) {
        $model = {{ $model }}Service::create($request->validated());

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
    public function update(${{ Str::camel($model) }}Id, {{ $model }}UpdateRequest $request) {
        $model = {{ $model }}Service::update(${{ Str::camel($model) }}Id, $request->validated());

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
    public function destroy(${{ Str::camel($model) }}Id) {
        $model = {{ $model }}Service::delete(${{ Str::camel($model) }}Id);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
