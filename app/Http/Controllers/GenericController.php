<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GenericController extends Controller
{
    protected $allowedModels = [
        'users',
    ];
    protected $model;
    public function __construct(Request $request) {
        $routeName = $request->route()?->getName();

        if (!$routeName || substr_count($routeName, '.') < 2) {
            return;
        }

        [$prefix, $model, $action] = explode('.', $routeName);
        $this->model = $model;
    }
    protected function resolveModel($model)
    {
        if (!in_array($model, $this->allowedModels)) {
            abort(403);
        }
        $class = 'App\\Models\\' . Str::studly(Str::singular($model));

        if (!class_exists($class)) {
            abort(404, 'Model not found');
        }

        return new $class;
    }

    protected function resolveResource($model)
    {
        $class = 'App\\Http\\Resources\\' . Str::studly(Str::singular($model)) . 'Resource';

        if (!class_exists($class)) {
            abort(404, 'Resource not found');
        }

        return new $class;
    }

    protected function validateRequest(Request $request, $resource, $method)
    {
        if (!method_exists($resource, 'rules')) {
            return $request->all();
        }

        $rules = $resource::rules($method);

        return Validator::make($request->all(), $rules)->validate();
    }

    public function index(Request $request)
    {    
        $modelClass = $this->resolveModel($this->model);
        $resource = $this->resolveResource($this->model);

        $query = $modelClass::query();

        if ($request->has('filter')) {
            foreach ($request->filter as $field => $value) {
                if (in_array($field, $resource->filters)) {
                    $query->where($field, 'like', "%{$value}%");
                }
            }
        }

        if ($request->has('include')) {
            $includes = explode(',', $request->include);

            $validIncludes = array_intersect(
                $includes,
                $resource->includes
            );

            $query->with($validIncludes);
        }

        if ($request->has('sort')) {
            $sorts = explode(',', $request->sort);

            foreach ($sorts as $sort) {
                $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
                $field = ltrim($sort, '-');

                if (in_array($field, $resource->sorts)) {
                    $query->orderBy($field, $direction);
                }
            }
        }
        return view("admin.{$this->model}.index", [
            'items' => $query->paginate(10),
            'model' => $this->model,
            'resource' => $resource,
        ]);
    }

    public function create()
    {
        return view("admin.automatic.create", [
            'model' => $this->model,
            'title' => 'Criar ' . Str::singular(ucfirst($this->model)),
            'route' => $this->model,
            'data' => null,
        ]);
    }


    public function store(Request $request)
    {
        $modelClass = $this->resolveModel($this->model);
        $instance = new $modelClass;

        $resource = $this->resolveResource($this->model);
        $data = $this->validateRequest($request, $resource, 'store');

        if (method_exists($instance, 'beforeStore')) {
            $data = $resource->beforeStore($data, $request);
        }

        $item = $modelClass::create($data);

        if (method_exists($instance, 'afterStore')) {
            $resource->afterStore($item, $request);
        }

        return $item;
    }


    public function show( $id)
    {
        $instance = $this->resolveModel($this->model);
        return $instance::findOrFail($id);
    }


    public function edit( $id)
    {
        $instance = $this->resolveModel($this->model);
        return $instance::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $modelClass = $this->resolveModel($this->model);
        $instance = new $modelClass;
        $resource = $this->resolveResource($this->model);
        $data = $this->validateRequest($request, $resource, 'update');

        $item = $modelClass::findOrFail($id);
        

        if (method_exists($resource, 'beforeUpdate')) {
            $data = $resource->beforeUpdate($item, $data, $request);
        }

        $item->update($data);

        if (method_exists($resource, 'afterUpdate')) {
            $resource->afterUpdate($item, $data, $request);
        }

        return $item;
    }


    public function destroy(Request $request, $id)
    {
        $modelClass = $this->resolveModel($this->model);
        $resource = $this->resolveResource($this->model);
        $item = $modelClass::findOrFail($id);

        if (method_exists($resource, 'beforeDelete')) {
            $resource->beforeDelete($item, $request);
        }

        $item->delete();

        if (method_exists($resource, 'afterDelete')) {
            $resource->afterDelete($item, $request);
        }

        return response()->json(['deleted' => true]);
    }
}
