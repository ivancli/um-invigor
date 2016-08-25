<?php

namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Invigor\UM\UMPermission;

class UMPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $format
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function index(Request $request, $format = null)
    {
        switch ($format) {
            case "datatable":
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
                if ($request->has('search') && $request->get('search')['value'] != '') {
                    $output->recordsFiltered = $permissions->count();
                } else {
                    $output->recordsFiltered = UMPermission::count();
                }
                $output->data = $permissions->toArray();
                break;
            default:
                $output = UMPermission::all();
        }
        return $output;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool|\Illuminate\Http\Response|UMPermission
     */
    public function store(Request $request)
    {
        try {
            /* insert */
            $permission = UMPermission::create($request->all());

            /* attach role */
            if ($request->has('role_id') && is_array($request->get('role_id'))) {
                $permission->roles()->attach($request->get('role_id'));
            }

            return $permission;
        } catch (Exception $e) {
            return false;
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
            return $permission;
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
            $permission = UMPermission::findOrFail($id);
            return $permission;
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
            $permission = UMPermission::findOrFail($id);
            $permission->update($request->all());
            /* attach role */
            if ($request->has('role_id') && is_array($request->get('role_id'))) {
                $permission->roles()->sync($request->get('role_id'));
            }

            return $permission;
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
            $permission = UMPermission::findOrFail($id);
            $permission->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}
