namespace {{ $namespace }}\{{ $module }}\Database\Models;

@if($has_deleted)
	use Illuminate\Database\Eloquent\SoftDeletes;
@endif
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use {{ $namespace }}\{{ $module }}\Database\Observers\{{ $model }}Observer;
use NextDeveloper\Commons\Database\Traits\UuidId;

/**
* Class {{ $model }}.
*
* @package {{ $namespace }}\{{ $module }}\Database\Models
*/
class {{ $model }} extends Model
{
use Filterable, UuidId;
@if($has_deleted)
	use SoftDeletes;
@endif


@if($hasTimestamps)
	public $timestamps = true;
@else
	public $timestamps = false;
@endif

protected $table = '{{$table}}';


/**
* @var array
*/
protected $guarded = [];

/**
*  Here we have the fulltext fields. We can use these for fulltext search if enabled.
*/
protected $fullTextFields = [
{{ $fullTextFields ?? '' }}
];

/**
* @var array
*/
protected $appends = [
{{ $appends ?? '' }}
];

/**
* We are casting fields to objects so that we can work on them better
* @var array
*/
protected $casts = [
{{ $casts ?? '' }}
];

/**
* We are casting data fields.
* @var array
*/
protected $dates = [
{{ $dates ?? '' }}
];

/**
* @var array
*/
protected $with = [

];

/**
* @var int
*/
protected $perPage = {{ $perPage }};

/**
* @return void
*/
public static function boot()
{
parent::boot();

//  We create and add Observer even if we wont use it.
parent::observe({{ $model }}Observer::class);

self::registerScopes();
}

public static function registerScopes()
{
$globalScopes = config('{{$lcModule}}.scopes.global');
$modelScopes = config('{{$lcModule}}.scopes.{{$table}}');

if(!$modelScopes) $modelScopes = [];
if (!$globalScopes) $globalScopes = [];

$scopes = array_merge(
$globalScopes,
$modelScopes
);

if($scopes) {
foreach ($scopes as $scope) {
static::addGlobalScope(app($scope));
}
}
}

// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}