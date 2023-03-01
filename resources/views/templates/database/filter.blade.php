namespace {{ $namespace }}\{{ $module }}\Database\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class {{ $model }}QueryFilter extends AbstractQueryFilter
{
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
        return $this->builder->where('{{$fieldName}}', 'like', '%' . $value . '%');
    }
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

        return $this->builder->where('{{$fieldName}}', $operator, $value);
    }
    
@endforeach
@foreach( $filterDateFields as $field )
    @php
    $fieldName = Str::camel($field);
    @endphp
public function {{$fieldName}}Start($date) 
    {
        return $this->builder->where( '{{$fieldName}}', '>=', $date );
    }

    public function {{$fieldName}}End($date) 
    {
        return $this->builder->where( '{{$fieldName}}', '<=', $date );
    }

@endforeach
@foreach( $idRefFields as $field )
    @php
    $functionName = Str::camel($field);
    $fieldName = substr($functionName, 0, -2);
    $modelName = ucfirst($fieldName);
    @endphp
public function {{$functionName}}($value)
    {
        ${{$fieldName}} = {{$modelName}}::where('id_ref', $value)->first();

        if(${{$fieldName}}) {
            return $this->builder->where('{{$functionName}}', '=', ${{$fieldName}}->id);
        }
    }

@endforeach
}
