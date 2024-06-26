namespace {{ $namespace }}\{{ $module }}\Http\Transformers\AbstractTransformers;

use NextDeveloper\Commons\Database\Models\Addresses;
use NextDeveloper\Commons\Database\Models\Comments;
use NextDeveloper\Commons\Database\Models\Meta;
use NextDeveloper\Commons\Database\Models\PhoneNumbers;
use NextDeveloper\Commons\Database\Models\SocialMedia;
use NextDeveloper\Commons\Database\Models\Votes;
use NextDeveloper\Commons\Database\Models\Media;
use NextDeveloper\Commons\Http\Transformers\MediaTransformer;
use NextDeveloper\Commons\Database\Models\AvailableActions;
use NextDeveloper\Commons\Http\Transformers\AvailableActionsTransformer;
use NextDeveloper\Commons\Database\Models\States;
use NextDeveloper\Commons\Http\Transformers\StatesTransformer;
use NextDeveloper\Commons\Http\Transformers\CommentsTransformer;
use NextDeveloper\Commons\Http\Transformers\SocialMediaTransformer;
use NextDeveloper\Commons\Http\Transformers\MetaTransformer;
use NextDeveloper\Commons\Http\Transformers\VotesTransformer;
use NextDeveloper\Commons\Http\Transformers\AddressesTransformer;
use NextDeveloper\Commons\Http\Transformers\PhoneNumbersTransformer;
@php
    if(
        $model != 'Domains' ||
        $model != 'Addresses' ||
        $model != 'Comments' ||
        $model != 'Meta' ||
        $model != 'PhoneNumbers' ||
        $model != 'SocialMedia' ||
        $model != 'Votes' ||
        $model != 'Media' ||
        $model != 'AvailableActions' ||
        $model != 'States'
        )
        echo 'use ' . $namespace . '\\' . $module . '\\Database\\Models\\' . $model . ';' . PHP_EOL;
@endphp
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

/**
 * Class {{ $model }}Transformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package {{ $namespace }}\{{ $module }}\Http\Transformers
 */
class Abstract{{ $model }}Transformer extends AbstractTransformer {

    /**
    * @var array
    */
    protected array $availableIncludes = [
        'states',
        'actions',
        'media',
        'comments',
        'votes',
        'socialMedia',
        'phoneNumbers',
        'addresses',
        'meta'
    ];

    /**
     * @param {{ $model }} $model
     *
     * @return array
     */
    public function transform({{ $model }} $model) {
            @foreach($idFields as $field)
                @if($field != 'object_id' || $field != 'object_type')
                    ${{ $field[2] }} = {{ $field[0] }}::where('id', $model->{{ $field[1] }})->first();
                @endif
        @endforeach

        return $this->buildPayload([
@php
        foreach($returnData as $item) {
        	$isIdField = false;

        	foreach($idFields as $field) {
        		if($field[1] == $item['field']) {
        			$isIdField = true;
        			break;
			    }
        	}

        	if($isIdField) {
                if($item['field'] != 'object_id' && $item['field'] != 'object_type')
			        echo '\'' . $item['field'] . '\'  =>  $' . $field[2] . ' ? $' . $field[2] . '->uuid : null,' . PHP_EOL;
        	} else {
        		echo '\'' . $item['field'] . '\'  =>  $model->' . $item['return'] . ',' . PHP_EOL;
        	}
        }
@endphp
    ]);
    }

    public function includeStates({{ $model }} $model)
    {
        $states = States::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($states, new StatesTransformer());
    }

    public function includeActions({{ $model }} $model)
    {
        $input = get_class($model);
        $input = str_replace('\\Database\\Models', '', $input);

        $actions = AvailableActions::withoutGlobalScope(AuthorizationScope::class)
            ->where('input', $input)
            ->get();

        return $this->collection($actions, new AvailableActionsTransformer());
    }

    public function includeMedia({{ $model }} $model)
    {
        $media = Media::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($media, new MediaTransformer());
    }

    public function includeSocialMedia({{ $model }} $model)
    {
        $socialMedia = SocialMedia::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($socialMedia, new SocialMediaTransformer());
    }

    public function includeComments({{ $model }} $model)
    {
        $comments = Comments::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($comments, new CommentsTransformer());
    }

    public function includeVotes({{ $model }} $model)
    {
        $votes = Votes::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($votes, new VotesTransformer());
    }

    public function includeMeta({{ $model }} $model)
    {
        $meta = Meta::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($meta, new MetaTransformer());
    }

    public function includePhoneNumbers({{ $model }} $model)
    {
        $phoneNumbers = PhoneNumbers::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($phoneNumbers, new PhoneNumbersTransformer());
    }

    public function includeAddresses({{ $model }} $model)
    {
        $addresses = Addresses::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($addresses, new AddressesTransformer());
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
