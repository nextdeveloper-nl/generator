namespace {{ $namespace }}\{{ $module }}\Http\Requests\{{ $model }};

class {{ $model }}{{ $requestType }}Request
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