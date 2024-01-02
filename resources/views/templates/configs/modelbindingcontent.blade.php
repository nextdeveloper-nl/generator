'{{ strtolower($model) }}' => function ($value) {
        return {{ $namespace }}\{{ $module }}\Database\Models\{{ $model }}::findByRef($value);
    },

// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE