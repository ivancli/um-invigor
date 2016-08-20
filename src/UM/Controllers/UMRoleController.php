<?php

namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Invigor\UM\Middleware\UMRole;


class UMRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = UMRole::all();
        $status = true;
        $length = $roles->length;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'roles', 'length']));
            } else {
                return $roles;
            }
        } else {
            /* TODO assign view */
            return view('')->with(compact(['roles', 'status', 'length']));
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
            if ($request->has('permission_id') && is_array($request->get('user_id'))) {
                $role->permissions()->attach($request->get('permission_id'));
            }
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['role', 'status']));
                } else {
                    return $role;
                }
            } else {
                /* TODO assign route */
                return redirect()->route('')->with(compact(['role', 'status']));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response|string
     */
    public function show(Request $request, $id)
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
                /* TODO assign view */
                return view('')->with(compact(['role', 'status']));
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
     * @return bool|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $role = UMRole::findOrFail($id);
            /* TODO assign view */
            return view('')->with(compact(['role']));
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
            'name' => 'max:255|min:1|unique:roles,name,' . $id,
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
                    $role->permissions()->sync($request->get('permission_id'));
                }

                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['role', 'status']));
                    } else {
                        return $role;
                    }
                } else {
                    /* TODO assign view */
                    return view('')->with(compact(['role', 'status']));
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
     * @return bool|\Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
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
                /* TODO assign route */
                return redirect()->route('')->with(compact(['status']));
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
