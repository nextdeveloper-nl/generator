namespace {{ $namespace }}\{{ $module }}\Http\Requests\{{ $model }};

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class {{ $model }}{{ $requestType }}Request extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            {{ $rules ?? '' }}
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}