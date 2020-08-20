<?php

namespace Armincms\Fields;
 
use Illuminate\Http\Resources\MergeValue;
use Laravel\Nova\Http\Requests\NovaRequest; 

class Targomaan extends MergeValue 
{    
    /**
     * Create a new merge-value instance.
     * 
     * @param  \Closure|array  $fields
     * @return void
     */
    public function __construct($fields = [])
    {   
        parent::__construct($this->prepareFields(app(NovaRequest::class), $fields));
    }  
    
    /**
     * Create a new element.
     *
     * @return static
     */
    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }

    /**
     * Prepare the given fields.
     *
     * @param  \Closure|array  $fields
     * @return array
     */
    public function prepareFields(NovaRequest $request, $fields)
    {  
        return $this->isIndexRequest($request) ? $fields : [new TargomaanField($fields)];
    }  

    /**
     * Detect if is resource index page.
     * 
     * @param  \Laravel\Nova\Http\Requests\NovaRequest $request 
     * @return boolean              
     */
    public function isIndexRequest(NovaRequest $request)
    {
        return ! ($request->route()->hasParameter('resourceId') || $request->isCreateOrAttachRequest());
    }

    /**
     * Handle magic property.
     * 
     * @param  mixed $key 
     * @param  array $value   
     * @return          
     */
    public function __set($key, $value)
    {
        collect($this->data)->each(function($field) use ($key, $value) {
            $field->{$key} = $value;
        }); 
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
        collect($this->data)->each(function($field) use ($method, $args) {
            if(method_exists(TargomaanField::class, $method) && ! is_a($field, TargomaanField::class)) {
                return $this;
            }

            call_user_func_array([$field, $method], $args);
        });

        return $this;
    }
}
