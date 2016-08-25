<?php

namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Invigor\UM\UMPermission;
use Invigor\UM\UMRole;
use Illuminate\Support\Facades\Validator;


class UMRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $format
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response|\stdClass|static[]
     */
    public function index(Request $request, $format = null)
    {
        switch ($format) {
            case "datatable":
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
                if ($request->has('search') && $request->get('search')['value'] != '') {
                    $output->recordsFiltered = $roles->count();
                } else {
                    $output->recordsFiltered = UMRole::count();
                }
                $output->data = $roles->toArray();
                break;
            default:
                $output = UMRole::all();
        }
        return $output;
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool|\Illuminate\Http\Response|UMRole
     */
    public function store(Request $request)
    {
        try {

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

            return $role;
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
     */
    public function show(Request $request, $id)
    {
        try {
            $role = UMRole::findOrFail($id);
            return $role;
        } catch (ModelNotFoundException $e) {
            return false;
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
            return $role;
        } catch (ModelNotFoundException $e) {
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

            return $role;
        } catch (ModelNotFoundException $e) {
            return false;
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
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}
