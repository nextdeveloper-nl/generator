public function {{ Str::camel($model) }}()
    {
        return $this->hasMany({{$class}}::class);
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE