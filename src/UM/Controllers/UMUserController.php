<?php

namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Validator;

class UMUserController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = Config::get('auth.providers.users.model');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = (new $this->userModel)::all();
        $status = true;
        $length = $users->length;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'users', 'length']));
            } else {
                return $users;
            }
        } else {
            /* TODO assign view */
            return view('')->with(compact(['users', 'status', 'length']));
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
            'email' => 'email|max:255|min:1|unique:users,email'
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
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = (new $this->userModel)::create($input);

            /* attach role user */
            if ($request->has('role_id') && is_array($request->get('role_id'))) {
                $user->roles()->attach($request->get('role_id'));
            }

            /* attach group */
            if ($request->has('group_id') && is_array($request->get('group_id'))) {
                $user->groups()->attach($request->get('group_id'));
            }

            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['user', 'status']));
                } else {
                    return $user;
                }
            } else {
                /* TODO assign route */
                return redirect()->route('')->with(compact(['user', 'status']));
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
            $user = (new $this->userModel)::findOrFail($id);
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['user', 'status']));
                } else {
                    return $user;
                }
            } else {
                /* TODO assign view */
                return view('')->with(compact(['user', 'status']));
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return bool|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $user = (new $this->userModel)::findOrFail($id);
            /* TODO assign view */
            return view('')->with(compact(['user']));
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
     * @return string
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|max:255|min:1|unique:users,email,' . $id,
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
                $user = (new $this->userModel)::findOrFail($id);
                $input = $request->all();
                $input['password'] = bcrypt($input['password']);
                $user->update($input);

                /* sync role user */
                if ($request->has('role_id') && is_array($request->get('role_id'))) {
                    $user->roles()->sync($request->get('role_id'));
                }

                /* sync group user */
                if ($request->has('group_id') && is_array($request->get('group_id'))) {
                    $user->groups()->sync($request->get('group_id'));
                }

                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['user', 'status']));
                    } else {
                        return $user;
                    }
                } else {
                    /* TODO assign view */
                    return view('')->with(compact(['user', 'status']));
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
     * @return bool
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = (new $this->userModel)::findOrFail($id);
            $user->delete();
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
