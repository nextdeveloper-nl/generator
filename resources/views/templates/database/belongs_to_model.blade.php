public function {{ Str::camel($model) }}()
    {
        return $this->belongsTo({{$class}}::class);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE