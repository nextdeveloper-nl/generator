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
        return $this->builder->where('{{$field}}', 'like', '%' . $value . '%');
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

        return $this->builder->where('{{$field}}', $operator, $value);
    }
    
@endforeach
@foreach( $filterBooleanFields as $field )
    @php
    $fieldName = Str::camel($field);
    @endphp
public function {{ $fieldName }}()
    {
        return $this->builder->where('{{$field}}', true);
    }
    
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

@endforeach
@foreach( $idRefFields as $field )
    @php
    $functionName = Str::camel($field);
    $fieldName = substr($functionName, 0, -2);
    $modelName = ucfirst($fieldName);
    @endphp
public function {{$functionName}}($value)
    {
        ${{$fieldName}} = {{$modelName}}::where('uuid', $value)->first();

        if(${{$fieldName}}) {
            return $this->builder->where('{{$field}}', '=', ${{$fieldName}}->id);
        }
    }

@endforeach
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}