<?php

namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Invigor\UM\UMPermission;

class UMPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $view
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function index(Request $request, $view = null)
    {
        $permissions = UMPermission::all();
        $status = true;
        $length = count($permissions);
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'permissions', 'length']));
            } else {
                return $permissions;
            }
        } else {
            if (is_null($view)) {
                $view = 'um::permission.index';
            }
            return view($view)->with(compact(['permissions', 'status', 'length']));
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
        if (is_null($view)) {
            $view = 'um::permission.create';
        }
        return view($view);
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
                    $route = 'um::permission.index';
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
            if (is_null($view)) {
                $view = "um::permission.edit";
            }
            return view($view)->with(compact(['permission']));
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
            'name' => 'max:255|min:1|unique:permissions,name,' . $id,
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
