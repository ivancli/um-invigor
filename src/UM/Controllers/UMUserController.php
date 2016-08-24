<?php

namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

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
     * @param null $format
     * @return \Illuminate\Http\Response|\stdClass
     * @internal param null $view
     */
    public function index(Request $request, $format = null)
    {
        switch ($format) {
            case "datatable":
                $users = (new $this->userModel)::when($request->has('start'), function ($query) use ($request) {
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
                    $user->urls = array(
                        "show" => route('um.user.show', $user->id),
                        "edit" => route('um.user.edit', $user->id),
                        "delete" => route('um.user.destroy', $user->id)
                    );
                });
                $output = new \stdClass();
                $output->draw = (int)($request->has('draw') ? $request->get('draw') : 0);
                $output->recordsTotal = (new $this->userModel)::count();
                if ($request->has('search') && $request->get('search')['value'] != '') {
                    $output->recordsFiltered = $users->count();
                } else {
                    $output->recordsFiltered = (new $this->userModel)::count();
                }
                $output->data = $users->toArray();
                break;
            default:
                $output = (new $this->userModel)::all();
        }
        return $output;
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     * @internal param null $view
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool|\Illuminate\Http\Response
     * @internal param null $route
     * @internal param null $view
     */
    public function store(Request $request)
    {
        try {
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
            return $user;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response|string
     * @internal param null $view
     */
    public function show(Request $request, $id)
    {
        try {
            $user = (new $this->userModel)::findOrFail($id);
            return $user;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return bool|\Illuminate\Http\Response
     * @internal param null $view
     */
    public function edit($id)
    {
        try {
            $user = (new $this->userModel)::findOrFail($id);
            return $user;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return string
     * @internal param null $route
     */
    public function update(Request $request, $id)
    {
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

            return $user;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return bool
     * @internal param null $route
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = (new $this->userModel)::findOrFail($id);
            $user->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}
