<?php

namespace Armincms\Fields;

use Laravel\Nova\Fields\Downloadable; 
use Laravel\Nova\Fields\Field; 
use Laravel\Nova\Http\Requests\NovaRequest; 

class Targomaan extends Field implements Downloadable
{     
    /**
     * The panel's component.
     *
     * @var string
     */
    public $component = 'targomaan';

    /**
     * List of the fields.
     * 
     * @var array
     */
    protected $fields = [];

    /**
     * The attribute locale delimiter.
     * 
     * @var string
     */
    protected $delimiter = '::';

    /**
     * Indicates if the element should be shown on the index view.
     *
     * @var \Closure|bool
     */
    public $showOnIndex = false;

    /**
     * Create a new merge-value instance.
     * 
     * @param  \Closure|array  $fields
     * @return void
     */
    public function __construct(array $fields = [], array $locales = [])
    {   
        $this->displayLocales();
        $this->withActiveLocale(app()->getLocale());
        $this->withLocales($locales ?: $this->availableLocales());
        $this->fields = $this->prepareFields($fields);

        parent::__construct($this->getAttribute());
    }  

    /**
     * Get the specific attribute for the targomaan field.
     * 
     * @return string
     */
    private function getAttribute()
    { 
        return md5($this->fields()->map->attribute->implode('-'));
    }

    /**
     * Determin if should display locale toolabr.
     * 
     * @return string
     */
    private function displayLocales(bool $display = true)
    {
        return $this->withMeta([
            'displayLocales' => $display
        ]);
    }

    /**
     * Set the disaplyable locales.
     * 
     * @param  array  $locales 
     * @return $this          
     */
    public function withActiveLocale(string $activeLocale)
    {
        return $this->withMeta(compact('activeLocale'));
    }

    /**
     * Set the fields.
     * 
     * @param  array  $fields 
     * @return $this          
     */
    public function withFields(array $fields)
    { 
        $this->fields = $fields;

        return $this; 
    }

    /**
     * Set the delimiter.
     * 
     * @param  string  $delimiter 
     * @return $this          
     */
    public function withDelimiter(string $delimiter)
    { 
        $this->delimiter = $delimiter;

        return $this; 
    }

    /**
     * Set the disaplyable locales.
     * 
     * @param  array  $locales 
     * @return $this          
     */
    protected function withLocales(array $locales)
    {
        return $this->withMeta(compact('locales'));
    }

    /**
     * Get the available locales.
     * 
     * @return array
     */
    protected function availableLocales()
    {
        return app('nova-targomaan.locales');
    }

    /**
     * Get the available fields.
     * 
     * @return array
     */
    protected function fields()
    {  
        return collect($this->fields);
    }

    /**
     * Preapre transaltion fields.
     * 
     * @param  array  $fields
     * @return array        
     */
    protected function prepareFields(array $fields)
    { 
        $locales = (array) data_get($this->meta(), 'locales');

        return collect($locales)->flatMap(function($name, $locale) use ($fields) {
            return collect($fields)->map(function($field) use ($locale) {
                return $this->cloneFieldForLocale($field, $locale);
            });
        });   
    }

    /**
     * Find a given field by its attribute.
     *
     * @param  string  $attribute 
     * @return \Laravel\Nova\Fields\Field|null
     */
    public function findFieldByAttribute($attribute)
    {
        return $this->fields()->first(function($field) use ($attribute) {
            return isset($field->attribute) && $field->attribute == $attribute;
        });
    }

    /**
     * Create new fiwld for the given locale.
     * 
     * @param  \Laravel\Nova\Fields\Field $field  
     * @param  string $locale 
     * @return \Laravel\Nova\Fields\Field         
     */
    protected function cloneFieldForLocale($field, $locale)
    {
        $field = clone $field;
        $field->attribute = $field->attribute.$this->delimiter.$locale; 
        $field->withMeta(compact('locale'));

        return $field;
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
        $this->fields = $this->fields()
                             ->filter->isShownOnUpdate($request, $resource)
                             ->values()
                             ->all();

        return $this->fields()->isNotEmpty();
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
        $this->fields = $this->fields()
                             ->filter->isShownOnDetail($request, $resource)
                             ->values()
                             ->all();

        return $this->fields()->isNotEmpty();
    }

    /**
     * Check for showing when creating.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return bool
     */
    public function isShownOnCreation(NovaRequest $request): bool
    {
        $this->fields = $this->fields()
                             ->filter->isShownOnCreation($request)
                             ->values()
                             ->all();

        return $this->fields()->isNotEmpty();
    }

    /**
     * Resolve the field's value for display.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolveForDisplay($resource, $attribute = null)
    { 
        $this->fields()->each->resolveForDisplay($resource, $attribute);
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
        $this->fields()->each->resolve($resource, $attribute);
    }

    /**
     * Resolve the default value for an Action field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return void
     */
    public function resolveForAction($request)
    {
        $this->fields()->each->resolveForAction($resource)->all();
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
        $callbacks = $this->fields()->map->fill($request, $model)->filter();

        return function() use ($callbacks) {
            return $callbacks->each->__invoke();
        };
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  object  $model
     * @return mixed
     */
    public function fillForAction(NovaRequest $request, $model)
    {
        $callbacks = $this->fields()->map->fillForAction($request, $model)->filter();

        return function() use ($callbacks) {
            return $callbacks->each->__invoke();
        };
    }

    /**
     * Get the validation rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function getRules(NovaRequest $request)
    {
        return $this->fields()
                    ->flatMap->getRules($request)
                    ->merge(parent::getRules($request))
                    ->all();
    }

    /**
     * Get the creation rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array|string
     */
    public function getCreationRules(NovaRequest $request)
    {
        return $this->fields()
                    ->flatMap->getCreationRules($request)
                    ->merge(parent::getCreationRules($request))
                    ->all();
    } 

    /**
     * Get the update rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function getUpdateRules(NovaRequest $request)
    {
        return $this->fields()
                    ->flatMap->getUpdateRules($request)
                    ->merge(parent::getUpdateRules($request))
                    ->all();
    } 

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'fields' => $this->fields(),
        ]);
    }
}
