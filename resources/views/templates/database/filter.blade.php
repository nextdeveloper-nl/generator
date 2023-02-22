namespace {{ $namespace }}\{{ $module }}\Database\Filters;

class {{ $model }}QueryFilter
{
    /**
     * Askıya alınmış hesaplar.
     *
     * @return mixed
     */
    public function name($name)
    {
        return $this->builder->where('name', 'like', '%'.$name.'%');
    }

    //  if tintint -> boolean

    public function isActive($bool)
    {
        if($bool)
            return $this->builder->where('is_active', 1);
        else
            return $this->builder->where('is_active', 0);
    }
}
