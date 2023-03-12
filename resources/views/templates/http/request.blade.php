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

}