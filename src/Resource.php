<?php 

namespace Armincms\Bios;

use Closure;
  
use Illuminate\Http\Request;   
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource as NovaResource; 
use Laravel\Nova\Contracts\ListableField;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Contracts\Resolvable; 
 
abstract class Resource extends NovaResource
{    
    /**
     * Indicates if the resource should be displayed in the configuration sidebar.
     *
     * @var bool
     */
    public static $configurable = true;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = '';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Armincms\\Bios\\Option'; 

    /**
     * The option storage driver name.
     *
     * @var string
     */
    public static $store = null;

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;  

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Determine if this resource is available for bios navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function availableForBios(Request $request)
    {
        return static::$configurable;
    }

    /**
     * Get a fresh instance of the model represented by the resource.
     *
     * @return mixed
     */
    public static function fillModel(string $attribute, $value = null)
    { 
        if (is_null($value)) {
            $value = static::store()->get($attribute, $value);
        } 

        return static::newModel()->forceFill([ $attribute => $value ]); 
    }

    /**
     * Get the store tag name.
     *
     * @return string
     */
    public static function storeTag() : string
    { 
        return static::uriKey();
    }

    /**
     * Get the option store name.
     *
     * @return string
     */
    public static function store()
    { 
        return app('armincms.option')->store(static::$store ?? config('option.store'));
    }

    /**
     * Retrieve option by the key.
     *
     * @var  string $key
     * @var  mixed  $default
     * @return mixed
     */
    public static function option($key, $default = null)
    {
        return static::store()->get($key, $default);
    }

    /**
     * Indicate option existance.
     *
     * @var  string $key
     * @var  mixed  $default
     * @return mixed
     */
    public static function has($key, $default = null)
    {
        return static::store()->has();
    }

    /**
     * Retrieve all stored options.
     * 
     * @return array
     */
    public static function options()
    {
        return static::store()->tag(static::storeTag());
    }  

    /**
     * Resolve the given fields to their values.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Closure|null  $filter
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    protected function resolveFields(NovaRequest $request, Closure $filter = null)
    {
        $fields = $this->availableFields($request)->filter(function($field) {
            return ! $field instanceof ListableField;
        });

        if (! is_null($filter)) {
            $fields = $filter($fields);
        }

        $fields->whereInstanceOf(Resolvable::class)->each(function($field) {   
            $field->resolve(static::fillModel($field->attribute));

            if(static::store()->has($field->attribute)) { 
                $field->withMeta(['value' => $field->value]);
            }
        }); 

        return $fields->filter->authorize($request)->values();  
    } 


    /**
     * Resolve the detail fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    public function detailFields(NovaRequest $request)
    {
        return $this->resolveFields($request, function (FieldCollection $fields) use ($request) {
            return $fields->filter->isShownOnDetail($request, $this->resource);
        })->when($this->shouldAddActionsField($request), function ($fields) {
            return $fields->push($this->actionfield());
        })->each(function ($field) {
            if ($field instanceof Resolvable) { 
                $field->resolveForDisplay(static::fillModel($field->attribute));
            }
        });
    }

    /**
     * Return the location to redirect the user after update.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        return '/bios/'.static::uriKey();
    }
}