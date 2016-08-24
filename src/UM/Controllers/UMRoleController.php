<?php

namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Invigor\UM\UMPermission;
use Invigor\UM\UMRole;
use Illuminate\Support\Facades\Validator;


class UMRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_role', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_role', ['only' => ['index', 'show']]);
        $this->middleware('permission:update_role', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_role', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $view
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response|static[]
     */
    public function index(Request $request, $view = null)
    {

        if ($request->ajax()) {
            $roles = UMRole::when($request->has('start'), function ($query) use ($request) {
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
            $output->recordsTotal = UMRole::count();
            if($request->has('search') && $request->get('search')['value'] != ''){
                $output->recordsFiltered = $roles->count();
            }else{
                $output->recordsFiltered = UMRole::count();
            }
            $output->data = $roles->toArray();
            return response()->json($output);
        } else {
            if (is_null($view)) {
                $view = 'um::role.index';
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
        $permissions = UMPermission::pluck('display_name', 'id');
        if (is_null($view)) {
            $view = 'um::role.create';
        }
        return view($view)->with(compact(['permissions']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param null $route
     * @return \Illuminate\Http\Response|UMRole
     */
    public function store(Request $request, $route = null)
    {
        /*validation*/
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|min:1|unique:roles,name'
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
            $role = UMRole::create($request->all());

            /* attach role user */
            if ($request->has('user_id') && is_array($request->get('user_id'))) {
                $role->users()->attach($request->get('user_id'));
            }

            /* attach role permission */
            if ($request->has('permission_id') && is_array($request->get('permission_id'))) {
                $role->perms()->attach($request->get('permission_id'));
            }

            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['role', 'status']));
                } else {
                    return $role;
                }
            } else {
                if (is_null($route)) {
                    $route = 'um.role.index';
                }
                return redirect()->route($route)->with(compact(['role', 'status']));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @param null $view
     * @return \Illuminate\Http\Response|string
     */
    public function show(Request $request, $id, $view = null)
    {
        try {
            $role = UMRole::findOrFail($id);
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['role', 'status']));
                } else {
                    return $role;
                }
            } else {
                if (is_null($view)) {
                    $view = 'um::role.show';
                }
                return view($view)->with(compact(['role', 'status']));
            }
        } catch (ModelNotFoundException $e) {
            $status = false;
            $message = "Role not found";
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
            $role = UMRole::findOrFail($id);
            $permissions = UMPermission::pluck('display_name', 'id');
            if (is_null($view)) {
                $view = "um::role.edit";
            }
            return view($view)->with(compact(['role', 'permissions']));
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
            'name' => 'required|max:255|min:1|unique:roles,name,' . $id,
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
                $role = UMRole::findOrFail($id);

                /* update role */
                $role->update($request->all());

                /* update role user */
                if ($request->has('user_id') && is_array($request->get('user_id'))) {
                    $role->users()->sync($request->get('user_id'));
                }

                /* update role permission */
                if ($request->has('permission_id')) {
                    $role->perms()->sync($request->get('permission_id'));
                }

                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['role', 'status']));
                    } else {
                        return $role;
                    }
                } else {
                    if (is_null($route)) {
                        $route = "um.role.index";
                    }
                    return redirect()->route($route)->with(compact(['role', 'status']));
                }
            } catch (ModelNotFoundException $e) {
                $status = false;
                $message = "Role not found";
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
            $role = UMRole::findOrFail($id);
            $role->delete();
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status']));
                } else {
                    return $status;
                }
            } else {
                if (is_null($route)) {
                    $route = 'um.role.index';
                }
                return redirect()->route($route)->with(compact(['status']));
            }
        } catch (ModelNotFoundException $e) {
            $status = false;
            $message = "Role not found";
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
