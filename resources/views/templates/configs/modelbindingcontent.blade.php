'{{ strtolower($model) }}' => function ($value) {
        return {{ $namespace }}\{{ $module }}\Database\Models\{{ $model }}::findByRef($value);
    },

//!APPENDHERE