public function {{ Str::camel($model) }}() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany({{$class}}::class);
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE