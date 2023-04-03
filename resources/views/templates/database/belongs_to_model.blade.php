public function {{ $model }}()
@php
$className = ucfirst($model);
@endphp
    {
        return $this->belongsTo({{$className}}::class);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE