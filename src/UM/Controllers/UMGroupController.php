<?php
namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Invigor\UM\UMGroup;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/19/2016
 * Time: 12:58 PM
 */
class UMGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param bool $format
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response|static[]
     * @internal param bool $filter
     * @internal param null $view
     */
    public function index(Request $request, $format = null)
    {
        switch ($format) {
            case "datatable":
                //offset
                $groups = UMGroup::when($request->has('start'), function ($query) use ($request) {
                    return $query->skip($request->get('start'));
                })
                    //length
                    ->when($request->has('length'), function ($query) use ($request) {
                        return $query->take($request->get('length'));
                    })
                    //text filter
                    ->when($request->has('search'), function ($query) use ($request) {
                        return $query->where('name', 'LIKE', "%{$request->get('search')['value']}%")
                            ->orwhere('url', 'LIKE', "%{$request->get('search')['value']}%");
                    })
                    //sorting
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
                $output->recordsTotal = UMGroup::count();
                if ($request->has('search') && $request->get('search')['value'] != '') {
                    $output->recordsFiltered = $groups->count();
                } else {
                    $output->recordsFiltered = UMGroup::count();
                }
                $output->data = $groups->toArray();
                break;
            default:
                $output = UMGroup::all();
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
     * @return bool|UMGroup
     * @internal param null $route
     */
    public function store(Request $request)
    {
        try {
            /* insert */
            $group = UMGroup::create($request->all());

            /* attach user */
            if ($request->has('user_id') && is_array($request->get('user_id'))) {
                $group->users()->attach($request->get('user_id'));
            }
            return $group;
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
     * @internal param Request $request
     * @internal param null $view
     */
    public function show(Request $request, $id)
    {
        try {
            $group = UMGroup::findOrFail($id);
            return $group;
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
            $group = UMGroup::findOrFail($id);
            return $group;
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
     * @internal param null $route
     */
    public function update(Request $request, $id)
    {
        try {
            $group = UMGroup::findOrFail($id);
            $input = $request->all();
            if (!$request->has('active')) {
                $input['active'] = 0;
            }
            $group->update($input);

            /* sync user */
            if ($request->has('user_id') && is_array($request->get('user_id'))) {
                $group->users()->sync($request->get('user_id'));
            }
            return $group;
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
     * @internal param Request $request
     * @internal param null $route
     */
    public function destroy(Request $request, $id)
    {
        try {
            $group = UMGroup::findOrFail($id);
            $group->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}