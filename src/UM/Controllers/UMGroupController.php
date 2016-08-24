<?php
namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Invigor\UM\UMGroup;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 8/19/2016
 * Time: 12:58 PM
 */
class UMGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_group', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_group', ['only' => ['index', 'show']]);
        $this->middleware('permission:update_group', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_group', ['only' => ['destroy']]);
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
            $groups = UMGroup::when($request->has('start'), function ($query) use ($request) {
                return $query->skip($request->get('start'));
            })
                ->when($request->has('length'), function ($query) use ($request) {
                    return $query->take($request->get('length'));
                })
                ->when($request->has('search'), function ($query) use ($request) {
                    return $query->where('name', 'LIKE', "%{$request->get('search')['value']}%")
                        ->orwhere('website', 'LIKE', "%{$request->get('search')['value']}%");
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
            $output->recordsTotal = UMGroup::count();
            if($request->has('search') && $request->get('search')['value'] != ''){
                $output->recordsFiltered = $groups->count();
            }else{
                $output->recordsFiltered = UMGroup::count();
            }
            $output->data = $groups->toArray();
            return response()->json($output);
        } else {
            if (is_null($view)) {
                $view = 'um::group.index';
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
        if (is_null($view)) {
            $view = 'um::group.create';
        }
        return view($view);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param null $route
     * @return \Illuminate\Http\Response|UMGroup
     */
    public function store(Request $request, $route = null)
    {
        /*validation*/
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:groups|max:255|min:1',
            'active' => 'boolean',
            'website' => 'required|url|max:2083|min:1',
            'description' => 'max:255'
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
            $group = UMGroup::create($request->all());

            /* attach user */
            if ($request->has('user_id') && is_array($request->get('user_id'))) {
                $group->users()->attach($request->get('user_id'));
            }

            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['group', 'status']));
                } else {
                    return $group;
                }
            } else {
                if (is_null($route)) {
                    $route = 'um.group.index';
                }
                return redirect()->route($route)->with(compact(['group', 'status']));
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
            $group = UMGroup::findOrFail($id);
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['group', 'status']));
                } else {
                    return $group;
                }
            } else {
                if (is_null($view)) {
                    $view = 'um::group.show';
                }
                return view($view)->with(compact(['group', 'status']));
            }
        } catch (ModelNotFoundException $e) {
            $status = false;
            $message = "Group not found";
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
            $group = UMGroup::findOrFail($id);
            if (is_null($view)) {
                $view = "um::group.edit";
            }
            return view($view)->with(compact(['group']));
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
            'name' => 'required|max:255|min:1|unique:groups,name,' . $id,
            'active' => 'boolean',
            'website' => 'required|url|max:2083|min:1',
            'description' => 'max: 2048'
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

                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['group', 'status']));
                    } else {
                        return $group;
                    }
                } else {
                    if (is_null($route)) {
                        $route = "um.group.index";
                    }
                    return redirect()->route($route)->with(compact(['group', 'status']));
                }
            } catch (ModelNotFoundException $e) {
                $status = false;
                $message = "Group not found";
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
            $group = UMGroup::findOrFail($id);
            $group->delete();
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['status']));
                } else {
                    return $status;
                }
            } else {
                if (is_null($route)) {
                    $route = 'um.group.index';
                }
                return redirect()->route($route)->with(compact(['status']));
            }
        } catch (ModelNotFoundException $e) {
            $status = false;
            $message = "Group not found";
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