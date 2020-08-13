<?php

namespace Armincms\Fields;

use JsonSerializable;
use Illuminate\Http\Resources\MergeValue;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Contracts\Resolvable;
use Laravel\Nova\Fields\FieldElement; 
use Laravel\Nova\Fields\Field; 
use Laravel\Nova\Metable; 

class TargomaanField extends FieldElement implements JsonSerializable, Resolvable  
{   
    use Metable;

    /**
     * The attribute / column name of the field.
     *
     * @var string
     */
    public $attribute;

    /**
     * The panel's component.
     *
     * @var string
     */
    public $component = 'targomaan'; 

    /**
     * The data to be merged.
     *
     * @var array
     */
    public $fields;

    /**
     * Available translations locale.
     * 
     * @var string
     */
    protected static $locales = [];

    /**
     * The locale string delimiter.
     * 
     * @var string
     */
    protected static $delimiter = "::";

    /**
     * Indicates whether the detail toolbar should be visible on this panel.
     *
     * @var bool
     */
    public $showToolbar = true;

    /**
     * Indicates if the field was resolved as a pivot field.
     *
     * @var bool
     */
    public $pivot = false;

    /**
     * Create a new targoman field instance.
     * 
     * @param  \Closure|array  $fields
     * @return void
     */
    public function __construct($fields = [])
    {  
        $this->fields = $this->prepareFields($fields); 
    }  

    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        $this->fields->whereInstanceOf(Resolvable::class)->each(function($field) use ($resource, $attribute) {
            $field->resolve($resource, $attribute);  
        });
    }

    /**
     * Prepare the given fields.
     *
     * @param  \Closure|array  $fields
     * @return array
     */
    protected function prepareFields($fields)
    {
        return collect(is_callable($fields) ? $fields() : $fields)
        		->flatMap(function ($field) { 
		            return $field instanceof MergeValue ? $this->prepareFields($field->data) : [$field];
		        })
		        ->flatMap([$this, 'prepareTranslationFields'])
		        ->values();
    } 

    /**
     * Prepare translations of the given field.
     *
     * @param  \Closure|array  $fields
     * @return array
     */
    public function prepareTranslationFields($field)
    {
    	return collect(static::locales())->map(function($language, $locale) use ($field) {  
			return $field instanceof Field 
						? $this->prepareTranslationField($field, $locale, $language) 
						: $field;
		})->values(); 
    } 

    /**
     * Prepare translation of the given field.
     *
     * @param  \Closure|array  $fields
     * @return array
     */
    public function prepareTranslationField($field, $locale, $language)
    { 
		return tap(clone $field, function($field) use ($locale, $language) { 
            if(app()->getLocale() !== $locale) { 
                // ensures that first model create correctly
                $field->name = $field->name.(count(static::locales()) > 1 ? "({$language})" : '');
                $field->attribute = $field->attribute.static::delimiter().$locale;
                $field->hideFromIndex();
            }

            $field->withMeta([
                'locale' => $locale,
                'validationKey' => $field->attribute,
            ]); 
		});  
    }

    /**
     * Get the available locales for translation.
     * 
     * @return array
     */
    public static function locales() : array
    {
    	return static::$locales ?: [app()->getLocale() => app()->getLocale(), 'En' => 'English'];
    }

    /**
     * Set the available locales for translation.
     * 
     * @return array
     */
    public static function withLocales(array $locales) : array
    {
    	static::$locales = $locales;

    	return static::class; 
    }

    /**
     * Get the locale string delimiter.
     * 
     * @return string
     */
    public static function delimiter() : string
    {
        return static::$delimiter;
    }

    /**
     * Set the locale string delimiter.
     * 
     * @var  string $delimiter
     * @return string
     */
    public static function withDelimiter(string $delimiter)
    {
        static::$delimiter = $delimiter;

        return static::class;
    } 

    /**
     * Hidden the toolbar when showing this panel.
     *
     * @return $this
     */
    public function withoutToolbar()
    {
        $this->showToolbar = false;

        return $this;
    } 

    /**
     * Get the validation rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function getRules(NovaRequest $request)
    {
        return $this->fields->flatMap(function($field) use ($request) {
            return $field->getUpdateRules($request);
        });
    }

    /**
     * Get the creation rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array|string
     */
    public function getCreationRules(NovaRequest $request)
    {
        return $this->fields->flatMap(function($field) use ($request) {
            return $field->getUpdateRules($request);
        });
    } 

    /**
     * Get the update rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function getUpdateRules(NovaRequest $request)
    {
        return $this->fields->flatMap(function($field) use ($request) {
            return $field->getUpdateRules($request);
        });
    }

    /**
     * Determine if the field is readonly.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return bool
     */
    public function isReadonly(NovaRequest $request)
    {
        return $this->fields->every(function($field) use ($request) {
            return $field->isReadonly($request);
        });
    }

    /**
     * Determine if the field is required.
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return bool
     */
    public function isRequired(NovaRequest $request)
    {
        return $this->fields->every(function($field) use ($request) {
            return $field->isRequired($request);
        });
    } 

    /**
     * Check for showing when action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string $method
     * @param  mixed  $resource
     * @return this
     */
    protected function applyShownOn(NovaRequest $request, $method, $resource = null)
    {  
        return tap(parent::{$method}($request, $method), function($apply) use ($request, $resource, $method) {
            if($apply) {
                $this->fields = $this->fields->filter(function($field) use ($request, $resource, $method) {
                    return $field->{$method}($request, $resource);
                })->values(); 
            }
        });
    }
     
    /**
     * Check for showing when updating.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  mixed  $resource
     * @return bool
     */
    public function isShownOnUpdate(NovaRequest $request, $resource): bool
    {
        return $this->applyShownOn($request, __FUNCTION__, $resource);
    }
    
    /**
     * Check showing on index.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  mixed  $resource
     * @return bool
     */
    public function isShownOnIndex(NovaRequest $request, $resource): bool
    {  
        return $this->applyShownOn($request, __FUNCTION__, $resource);
    }
    
    /**
     * Check showing on detail.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  mixed  $resource
     * @return bool
     */
    public function isShownOnDetail(NovaRequest $request, $resource): bool
    {
        return $this->applyShownOn($request, __FUNCTION__, $resource);
    }
    
    /**
     * Check for showing when creating.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return bool
     */
    public function isShownOnCreation(NovaRequest $request): bool
    { 
        return $this->applyShownOn($request, __FUNCTION__);
    }

    /**
     * Prepare the panel for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [ 
            'prefixComponent'   => true,
            'showToolbar'       => $this->showToolbar,
            'component' => $this->component(),
            'locales'   => static::locales(),
            'fields'    => $this->fields->toArray(), 
            'active'    => app()->getLocale(),
            'delimiter' => static::delimiter(),
            'textAlign' => 'center'
        ]);
    }  

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  object  $model
     * @return mixed
     */
    public function fill(NovaRequest $request, $model)
    {
        $callbacks = $this->fields->map->fill($request, $model)->filter(function($callback) {
            return is_callable($callback);
        });

        return function() use ($callbacks) {
            $callbacks->each->__invoke(); 
        };
    } 

    /**
     * Handle magic method.
     * 
     * @param  mixed $method 
     * @param  array $args   
     * @return mixed         
     */
    public function __call($method, $args)
    {
        $this->fields->each(function($field) use ($method, $args) {
            call_user_func_array([$field, $method], $args);
        });

        return $this;
    }
}
