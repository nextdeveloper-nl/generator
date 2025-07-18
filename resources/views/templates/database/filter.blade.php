namespace {{ $namespace }}\{{ $module }}\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
@foreach( $idRefFields as $field )
    @if($field == 'iam_user_id')
        use NextDeveloper\Accounts\Database\Models\User;
    @endif
@endforeach


/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class {{ $model }}QueryFilter extends AbstractQueryFilter
{
@if($hasTagsField)
    /**
     * Filter by tags
     *
     * @param $values
     * @return Builder
     */
    public function tags($values) {
        $tags = explode(',', $values);

        $search = '';

        for($i = 0; $i < count($tags); $i++) {
            $search .= "'" . trim($tags[$i]) . "',";
        }

        $search = substr($search, 0, -1);

        return $this->builder->whereRaw('tags @> ARRAY[' . $search . ']');
    }
@endif

    /**
    * @var Builder
    */
    protected $builder;
@foreach( $filterTextFields as $field )
    @php
    $fieldName = Str::camel($field);
    @endphp

    public function {{ $fieldName }}($value)
    {
        return $this->builder->where('{{$field}}', 'ilike', '%' . $value . '%');
    }

    @if($field != $fieldName)
    //  This is an alias function of {{ $fieldName }}
    public function {{ $field }}($value)
    {
    return $this->{{ $fieldName }}($value);
    }
    @endif
@endforeach

@foreach( $filterNumberFields as $field )
    @php
    $fieldName = Str::camel($field);
    @endphp
public function {{ $fieldName }}($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
           $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('{{$field}}', $operator, $value);
    }

    @if($field != $fieldName)
    //  This is an alias function of {{ $fieldName }}
    public function {{ $field }}($value)
    {
    return $this->{{ $fieldName }}($value);
    }
    @endif

@endforeach
@foreach( $filterBooleanFields as $field )
    @php
    $fieldName = Str::camel($field);
    @endphp
public function {{ $fieldName }}($value)
    {
        return $this->builder->where('{{$field}}', $value);
    }

    @if($field != $fieldName)
    //  This is an alias function of {{ $fieldName }}
    public function {{ $field }}($value)
    {
    return $this->{{ $fieldName }}($value);
    }
     @endif

@endforeach
@foreach( $filterDateFields as $field )
    @php
    $fieldName = Str::camel($field);
    @endphp
public function {{$fieldName}}Start($date)
    {
        return $this->builder->where( '{{$field}}', '>=', $date );
    }

    public function {{$fieldName}}End($date)
    {
        return $this->builder->where( '{{$field}}', '<=', $date );
    }

    //  This is an alias function of {{ $fieldName }}
    public function {{ $field }}_start($value)
    {
    return $this->{{ $fieldName }}Start($value);
    }

    //  This is an alias function of {{ $fieldName }}
    public function {{ $field }}_end($value)
    {
    return $this->{{ $fieldName }}End($value);
    }

@endforeach
@foreach( $idRefFields as $field )
    @php
    $functionName = Str::camel($field['1']);
    $snakeFunctionName = Str::snake($functionName);
    $fieldName = substr($functionName, 0, -2);
    $modelName = ucfirst($fieldName);
    @endphp
public function {{$functionName}}($value)
    {
    @if(!$field[3])
        ${{$fieldName}} = {{$field[0]}}::where('uuid', $value)->first();

        if(${{$fieldName}}) {
            return $this->builder->where('{{$field[1]}}', '=', ${{$fieldName}}->id);
        }
    @else
        return $this->builder->where('{{$field[1]}}', '=', $value);
    @endif
    }

    @if(!($snakeFunctionName == 'iam_user_id' || $snakeFunctionName == 'iam_account_id'))
    //  This is an alias function of {{ $fieldName }}
    public function {{ $snakeFunctionName }}($value)
    {
    return $this->{{ $fieldName }}($value);
    }
    @endif

@endforeach
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
