<?php


namespace app\middleware;

class UnionCollect
{
    public function handle($request, \Closure $next)
    {
	    switch ($request->param('model')) {
            case 'id':
	            if(is_array($request->param('id'))){
	            	$request->id = implode(',',$request->param('id'));
	            }else{
	            	$request->id = $request->param('id',null);
	            }
                break;
            case 'today':
                 $request->time = $request->param('model');
                break;
        }
        return $next($request);
    }
}