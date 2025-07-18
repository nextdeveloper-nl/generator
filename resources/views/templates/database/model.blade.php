namespace {{ $namespace }}\{{ $module }}\Database\Models;

@if($has_deleted)
	use Illuminate\Database\Eloquent\SoftDeletes;
@endif
@if($has_sshable)
    use NextDeveloper\Commons\Database\Traits\SSHable;
    use NextDeveloper\IAAS\Database\Traits\Agentable;
@endif
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Commons\Database\Traits\HasStates;
use {{ $namespace }}\{{ $module }}\Database\Observers\{{ $model }}Observer;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
* {{ $model }} model.
*
* @package {{ $namespace }}\{{ $module }}\Database\Models
@foreach($documentation as $doc)
* {{ $doc }}
@endforeach
*/
class {{ $model }} extends Model
{
use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator;
@if($has_deleted)
	use SoftDeletes;
@endif
@if($has_sshable)
    use SSHable, Agentable;
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

protected $fillable = [
    @foreach($fillable as $item)
        '{{ $item }}',
    @endforeach
];

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
