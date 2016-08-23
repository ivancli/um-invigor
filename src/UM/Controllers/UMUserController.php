<?php

namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Invigor\UM\UMGroup;
use Invigor\UM\UMRole;

class UMUserController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = Config::get('auth.providers.users.model');
        $this->middleware('permission:create_user', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_user', ['only' => ['index', 'show']]);
        $this->middleware('permission:update_user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_user', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $view
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $view = null)
    {
        if ($request->ajax()) {
            $users = User::when($request->has('start'), function ($query) use ($request) {
                return $query->skip($request->get('start'));
            })
                ->when($request->has('length'), function ($query) use ($request) {
                    return $query->take($request->get('length'));
                })
                ->when($request->has('search'), function ($query) use ($request) {
                    return $query->where('name', 'LIKE', "%{$request->get('search')['value']}%")
                        ->orwhere('email', 'LIKE', "%{$request->get('search')['value']}%");
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
            $users->each(function ($user, $key) {
                $user->show_url = route('um.user.show', $user->id);
                $user->edit_url = route('um.user.edit', $user->id);
                $user->delete_url = route('um.user.destroy', $user->id);
            });
            $output = new \stdClass();
            $output->draw = (int)($request->has('draw') ? $request->get('draw') : 0);
            $output->recordsTotal = User::count();
            $output->recordsFiltered = User::count();
            $output->data = $users->toArray();
            return response()->json($output);
        } else {
            if (is_null($view)) {
                $view = 'um::user.index';
            }
            return view($view);
        }


        if ($request->has('search')) {
            $search = $request->get('search');
            $users = (new $this->userModel)::where('name', 'LIKE', "%{$search['value']}%")->orwhere('email', 'LIKE', "%{$search['value']}%")->paginate(10);
        } else {
            $users = (new $this->userModel)::paginate(10);
        }

        $status = true;
        $length = count($users);
        if ($request->ajax()) {
            dd($request->all());
            $output = new \stdClass();
            $output->draw = $request->has('draw') ? $request->get('draw') : 0;
            $output->recordsTotal = (new $this->userModel)::count();


            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'users', 'length', 'groups']));
            } else {
                return $users;
            }
        } else {
            if (is_null($view)) {
                $view = 'um::user.index';
            }
//            return view($view)->with(compact(['users', 'status', 'length', 'groups']));
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
        $groups = UMGroup::pluck('name', 'id');
        $roles = UMRole::pluck('display_name', 'id');
        if (is_null($view)) {
            $view = 'um::user.create';
        }
        return view($view)->with(compact(['groups', 'roles']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param null $route
     * @return \Illuminate\Http\Response
     * @internal param null $view
     */
    public function store(Request $request, $route = null)
    {
        /*validation*/
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
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
                return redirect()->back()->withErrors($validator)->withInput();
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
                if (is_null($route)) {
                    $route = 'um.user.index';
                }
                return redirect()->route($route)->with(compact(['user', 'status']));
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
            $user = (new $this->userModel)::findOrFail($id);
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['user', 'status']));
                } else {
                    return $user;
                }
            } else {
                if (is_null($view)) {
                    $view = 'um::user.show';
                }
                return view($view)->with(compact(['user', 'status']));
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
     * @param null $view
     * @return bool|\Illuminate\Http\Response
     */
    public function edit($id, $view = null)
    {
        try {
            $user = (new $this->userModel)::findOrFail($id);
            $groups = UMGroup::pluck('name', 'id');
            $roles = UMRole::pluck('display_name', 'id');
            if (is_null($view)) {
                $view = "um::user.edit";
            }
            return view($view)->with(compact(['user', 'groups', 'roles']));
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
     * @return string
     */
    public function update(Request $request, $id, $route = null)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'min:6|confirmed',
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
                if ($request->has('password') && $request->get('password') != '') {
                    $input['password'] = bcrypt($input['password']);
                } else {
                    unset($input['password']);
                }
                $user->update($input);

                /* sync role user */
                if ($request->has('role_id') && is_array($request->get('role_id'))) {
                    $user->roles()->sync($request->get('role_id'));
                } else {
                    $user->roles()->detach();
                }

                /* sync group user */
                if ($request->has('group_id') && is_array($request->get('group_id'))) {
                    $user->groups()->sync($request->get('group_id'));
                } else {
                    $user->groups()->detach();
                }

                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['user', 'status']));
                    } else {
                        return $user;
                    }
                } else {
                    if (is_null($route)) {
                        $route = "um.user.index";
                    }
                    return redirect()->route($route)->with(compact(['user', 'status']));
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
     * @return bool
     */
    public function destroy(Request $request, $id, $route = null)
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
                if (is_null($route)) {
                    $route = 'um.user.index';
                }
                return redirect()->route($route)->with(compact(['status']));
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
