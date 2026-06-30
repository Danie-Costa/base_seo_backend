<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CheckPermission
{
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()?->getName();

        if ($routeName && str_contains($routeName, '{model}')) {
            $parts = explode('.', $routeName);
            if (count($parts) === 3) {
                [$prefix, $model, $action] = $parts;
                $model = $request->route('model');
                $routeName = "{$prefix}.{$model}.{$action}";
            }
        }

        $userRule = 'public';
        if(auth()->user()){
            $userRule = auth()->user()->rule; 
        }
       
        $permissions = config('permissions.rules');
        if (!isset($permissions['public']) || !in_array($routeName, $permissions['public'])) {
            if (!isset($permissions[$userRule]) || !in_array($routeName, $permissions[$userRule])) {
                return redirect('/unauthorized');
            }
        }
        
        $crudMenu = $this->getCrudMenu($userRule);
        $menu = $this->getMenu($userRule);
        
        app('view')->composer('*', function ($view) use ($menu) {
            $view->with('menu', $menu);
        });
        
        app('view')->composer('*', function ($view) use ($crudMenu) {
            $view->with('crudMenu', $crudMenu);
        });
        
     
        
        return $next($request);
    }
    public function getMenu($userRule)
    {
        $groupsPermissions = config('menu');
        return $groupsPermissions[$userRule] ?? [];
    }

    public function getCrudMenu($userRule)
    {
        $permissions = config('permissions.rules');
        return $permissions[$userRule] ?? [];
    }




}
