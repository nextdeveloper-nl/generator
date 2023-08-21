namespace {{ $namespace }}\{{ $module }}\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use {{ $namespace }}\{{ $module }}\Database\Models\{{ $model }};
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use {{ $namespace }}\{{ $module }}\Http\Transformers\AbstractTransformers\Abstract{{ $model }}Transformer;

/**
 * Class {{ $model }}Transformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package {{ $namespace }}\{{ $module }}\Http\Transformers
 */
class {{ $model }}Transformer extends Abstract{{ $model }}Transformer {

    /**
     * @param {{ $model }} $model
     *
     * @return array
     */
    public function transform({{ $model }} $model) {
        $transformed = Cache::get(
            CacheHelper::getKey('{{ $model }}', $model->uuid, 'Transformed')
        );

        if($transformed)
            return $transformed;

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('{{ $model }}', $model->uuid, 'Transformed'),
            $transformed
        );

        return parent::transform($model);
    }
}
