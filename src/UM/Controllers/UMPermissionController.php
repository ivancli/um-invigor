<?php

namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Invigor\UM\UMPermission;
use Invigor\UM\UMRole;

class UMPermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_permission', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_permission', ['only' => ['index', 'show']]);
        $this->middleware('permission:update_permission', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_permission', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $view
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function index(Request $request, $view = null)
    {
        if ($request->ajax()) {
            $permissions = UMPermission::with('parentPerm')->when($request->has('start'), function ($query) use ($request) {
                return $query->skip($request->get('start'));
            })
                ->when($request->has('length'), function ($query) use ($request) {
                    return $query->take($request->get('length'));
                })
                ->when($request->has('search'), function ($query) use ($request) {
                    return $query->where('name', 'LIKE', "%{$request->get('search')['value']}%")
                        ->orwhere('display_name', 'LIKE', "%{$request->get('search')['value']}%");
                })
                ->when($request->has('order') && is_array($request->get('order')), function ($query) use ($request) {
                    $order = $request->get('order');
                    $columns = $request->get('columns');
                    foreach ($order as $index => $ord) {
                        if (isset($ord['column']) && isset($columns[$ord['column']])) {
                            $name = $columns[$ord['column']]['name'];
                            $direction = $ord['dir'];
                            $query->orderBy($name, $direction);
                        }
                    }
                    return $query;
                })->get();
            $output = new \stdClass();
            $output->draw = (int)($request->has('draw') ? $request->get('draw') : 0);
            $output->recordsTotal = UMPermission::count();
            if($request->has('search') && $request->get('search')['value'] != ''){
                $output->recordsFiltered = $permissions->count();
            }else{
                $output->recordsFiltered = UMPermission::count();
            }
            $output->data = $permissions->toArray();
            return response()->json($output);
        } else {
            if (is_null($view)) {
                $view = 'um::permission.index';
            }
            return view($view);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param null $view
     * @return \Illuminate\Http\Response
     */
    public function create($view = null)
    {
        $roles = UMRole::pluck('display_name', 'id');
        $permissions = UMPermission::pluck('display_name', 'id');
        if (is_null($view)) {
            $view = 'um::permission.create';
        }
        return view($view)->with(compact(['roles', 'permissions']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param null $route
     * @return \Illuminate\Http\Response|UMPermission
     * @internal param null $view
     */
    public function store(Request $request, $route = null)
    {
        /*validation*/
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|min:1|unique:permissions,name'
        ]);
        if ($validator->fails()) {
            $status = false;
            if ($request->ajax()) {
                $errors = $validator->errors()->all();
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'errors']));
                } else {
                    return $errors;
                }
            } else {
                return redirect()->back()->withInput()->withErrors($validator);
            }
        } else {
            /* insert */
            $permission = UMPermission::create($request->all());

            /* attach role */
            if ($request->has('role_id') && is_array($request->get('role_id'))) {
                $permission->roles()->attach($request->get('role_id'));
            }

            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['permission', 'status']));
                } else {
                    return $permission;
                }
            } else {
                if (is_null($route)) {
                    $route = 'um.permission.index';
                }
                return redirect()->route($route)->with(compact(['permission', 'status']));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @param null $view
     * @return string
     */
    public function show(Request $request, $id, $view = null)
    {
        try {
            $permission = UMPermission::findOrFail($id);
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['permission', 'status']));
                } else {
                    return $permission;
                }
            } else {
                if (is_null($view)) {
                    $view = 'um::permission.show';
                }
                return view($view)->with(compact(['permission', 'status']));
            }
        } catch (ModelNotFoundException $e) {
            $status = false;
            $message = "Permission not found";
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'message']));
                } else {
                    return $message;
                }
            } else {
                abort(404, "Page not found");
                return false;
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @param null $view
     * @return bool|\Illuminate\Http\Response
     */
    public function edit($id, $view = null)
    {
        try {
            $permission = UMPermission::findOrFail($id);

            /*parent permission should not be the child of its child permissions*/
            $exceptIDs = [$permission->id];
            if (!is_null($permission->childPerms)) {
                $exceptIDs = array_merge($permission->childPerms->pluck('id')->toArray(), $exceptIDs);
            }
            $permissions = UMPermission::all()->except($exceptIDs)->pluck('display_name', 'id');
            $roles = UMRole::pluck('display_name', 'id');
            if (is_null($view)) {
                $view = "um::permission.edit";
            }
            return view($view)->with(compact(['permission', 'roles', 'permissions']));
        } catch (ModelNotFoundException $e) {
            abort(404, "Page not found");
            return false;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @param null $route
     * @return \Illuminate\Http\Response|string
     */
    public function update(Request $request, $id, $route = null)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|min:1|unique:permissions,name,' . $id,
        ]);
        if ($validator->fails()) {
            $status = false;
            $errors = $validator->errors()->all();
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['errors', 'status']));
                } else {
                    return $errors;
                }
            } else {
                return redirect()->back()->withInput()->withErrors($validator);
            }
        } else {
            try {
                $permission = UMPermission::findOrFail($id);
                $permission->update($request->all());
                /* attach role */
                if ($request->has('role_id') && is_array($request->get('role_id'))) {
                    $permission->roles()->sync($request->get('role_id'));
                }

                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['permission', 'status']));
                    } else {
                        return $permission;
                    }
                } else {
                    if (is_null($route)) {
                        $route = "um.permission.index";
                    }
                    return redirect()->route($route)->with(compact(['permission', 'status']));
                }
            } catch (ModelNotFoundException $e) {
                $status = false;
                $message = "User not found";
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['status', 'message']));
                    } else {
                        return $message;
                    }
                } else {
                    abort(404, "Page not found");
                    return false;
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @param null $route
     * @return bool|\Illuminate\Http\Response
     */
    public function destroy(Request $request, $id, $route = null)
    {
        try {
            $permission = UMPermission::findOrFail($id);
            $permission->delete();
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status']));
                } else {
                    return $status;
                }
            } else {
                if (is_null($route)) {
                    $route = 'um.permission.index';
                }
                return redirect()->route($route)->with(compact(['status']));
            }
        } catch (ModelNotFoundException $e) {
            $status = false;
            $message = "Permission not found";
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status', 'message']));
                } else {
                    return $message;
                }
            } else {
                abort(404, "Page not found");
                return false;
            }
        }
    }
}
