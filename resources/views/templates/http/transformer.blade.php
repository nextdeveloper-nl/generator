namespace {{ $namespace }}\{{ $module }}\Http\Transformers;

use {{ $namespace }}\{{ $module }}\Database\Models\{{ $model }};
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class {{ $model }}Transformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package {{ $namespace }}\{{ $module }}\Http\Transformers
 */
class {{ $model }}Transformer extends AbstractTransformer {

    /**
     * @param {{ $model }} $model
     *
     * @return array
     */
    public function transform({{ $model }} $model) {
        return $this->buildPayload([
    @foreach($returnData as $item)
        '{{ $item['field'] }}'  =>  $model->{{ $item['return'] }},
    @endforeach
    ]);
    }
}
