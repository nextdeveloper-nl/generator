public function {{ $model }}()
@php
$className = ucfirst(Str::singular($model));
@endphp
    {
        return $this->hasMany({{$className}}::class);
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE