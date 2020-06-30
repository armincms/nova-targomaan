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
     * Handle magic method.
     * 
     * @param  mixed $method 
     * @param  array $args   
     * @return mixed         
     */
    public function __call($method, $args)
    {
        collect($this->data)->each(function($field) use ($method, $args) {
            call_user_func_array([$field, $method], $args);
        });

        return $this;
    }
}
