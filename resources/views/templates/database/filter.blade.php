namespace {{ $namespace }}\{{ $module }}\Database\Filters;

use Illuminate\Database\Eloquent\Builder;

class {{ $model }}QueryFilter
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
    
}
