<?php
namespace Invigor\UM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
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
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response|static[]
     */
    public function index(Request $request)
    {
        $groups = UMGroup::all();
        $status = true;
        $length = $groups->length;
        if ($request->ajax()) {
            if ($request->wantsJson()) {
                return response()->json(compact(['status', 'groups', 'length']));
            } else {
                return $groups;
            }
        } else {
            /* TODO assign view */
            return view('')->with(compact(['groups', 'status', 'length']));
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
     * @return \Illuminate\Http\Response|UMGroup
     */
    public function store(Request $request)
    {
        /*validation*/
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|unique:groups|max:255|min:1',
            'website' => 'required|url|max:2083|min:1',
            'description' => 'max: 2048'
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
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['group', 'status']));
                } else {
                    return $group;
                }
            } else {
                /* TODO assign route */
                return redirect()->route('')->with(compact(['group', 'status']));
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
            $group = UMGroup::findOrFail($id);
            $status = true;
            if ($request->ajax()) {
                if ($request->wantsJson()) {
                    return response()->json(compact(['group', 'status']));
                } else {
                    return $group;
                }
            } else {
                /* TODO assign view */
                return view('')->with(compact(['group', 'status']));
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
     * @return bool|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $group = UMGroup::findOrFail($id);
            /* TODO assign view */
            return view('')->with(compact(['group']));
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
            'group_name' => 'max:255|min:1|unique:groups,group_name,' . $id,
            'website' => 'url|max:2083|min:1',
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
                $group->update($request->all());
                $status = true;
                if ($request->ajax()) {
                    if ($request->wantsJson()) {
                        return response()->json(compact(['group', 'status']));
                    } else {
                        return $group;
                    }
                } else {
                    /* TODO assign view */
                    return view('')->with(compact(['group', 'status']));
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
     * @return bool|\Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
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
                /* TODO assign route */
                return redirect()->route('')->with(compact(['status']));
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