<?php

namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Invigor\UM\UMPermission;

class UMPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function index(Request $request)
    {
        $permissions = UMPermission::all();
        $status = true;
        $length = $permissions->length;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'permissions', 'length']));
            } else {
                return $permissions;
            }
        } else {
            /* TODO assign view */
            return view('')->with(compact(['permissions', 'status', 'length']));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* TODO assign view */
        return view('');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|UMPermission
     */
    public function store(Request $request)
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
                /* TODO assign route */
                return redirect()->route('')->with(compact(['permission', 'status']));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return string
     */
    public function show(Request $request, $id)
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
                /* TODO assign view */
                return view('')->with(compact(['permission', 'status']));
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
     * @return bool|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $permission = UMPermission::findOrFail($id);
            /* TODO assign view */
            return view('')->with(compact(['permission']));
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
     * @return \Illuminate\Http\Response|string
     */
    public function update(Request $request, $id)
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
                    /* TODO assign view */
                    return view('')->with(compact(['permission', 'status']));
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
     * @return bool|\Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
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
                /* TODO assign route */
                return redirect()->route('')->with(compact(['status']));
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
