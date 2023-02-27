namespace {{ $namespace }}\{{ $module }}\Database\Filters;

use Illuminate\Database\Eloquent\Builder;

class {{ $model }}QueryFilter
{

    /**
    * @var Builder
    */
    protected $builder;

@foreach( $filterTextFields as $field )
    public function {{ $field }}($value)
    {
        return $this->builder->where('{{$field}}', 'like', '%' . $value . '%');
    }

@endforeach

@foreach( $filterNumberFields as $field )
    public function {{ $field }}($value)
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
    
}
