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
        @foreach($idFields as $field)
        ${{ $field[2] }} = {{ $field[0] }}::where('id', $model->{{ $field[1] }})->first();
	@endforeach
    
        return $this->buildPayload([
@php
        foreach($returnData as $item) {
        	$isIdField = false;
        	
        	foreach($idFields as $field) {
        		if($field[1] == $item['field']) {
        			$isIdField = true;
        			break;
			}
        	}
        	
        	if($isIdField) {
        		echo '\'' . $item['field'] . '\'  =>  $' . $field[2] . '->uuid,' . PHP_EOL;
        	} else {
        		echo '\'' . $item['field'] . '\'  =>  $model->' . $item['return'] . ',' . PHP_EOL;
        	}
        }
@endphp
    ]);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
