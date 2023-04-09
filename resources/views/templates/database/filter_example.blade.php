namespace {{ $namespace }}\{{ $module }}\Database\Filters;

use Illuminate\Database\Eloquent\Builder;

class {{ $model }}QueryFilter
{

    /**
    * @var Builder
    */
    protected $builder;

    public function name($name)
    {
        return $this->builder->where('name', 'like', '%'.$name.'%');
    }

    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function my()
    {
       return $this->builder->where('representative_user_id', getAUUser()->id_ref);
    }

    public function id($id)
    {
       return $this->builder->where('id_ref', 'like', '%'.$id.'%');
    }

    public function phone($value)
    {
       return $this->builder->where('phone', 'like', '%' . $value . '%');
    }

    public function email($value)
    {
      return $this->builder->where('user_email', 'like', '%' . $value . '%');
    }

    public function phoneNumber($value)
    {
      return $this->builder->where('phone_number', 'like', '%' . $value . '%');
    }

    public function balance($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
           $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('balance', $operator, $value);
    }

    public function credit($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('credit', $operator, $value);
    }

    public function creditScore($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('credit_score', $operator, $value);
    }

    public function accountType($value)
    {
    return $this->builder->where('account_type', 'like', '%' . $value . '%');
    }

    public function representativeUserName($value)
    {
    return $this->builder->where('representative_user_name', 'like', '%' . $value . '%');
    }

    public function representativeAccountName($value)
    {
    return $this->builder->where('representative_account_name', 'like', '%' . $value . '%');
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}