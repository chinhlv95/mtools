<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\UpdateBugTypeRequest;
use App\Models\BugType;
use Illuminate\Support\Facades\Config;

class BugTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bug_type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $bugType = new BugType();
        $bugType->name = trim($request->name);
        $bugType->key = $request->key;
        $bugType->related_id = $request->key;
        dd($bugType);
        $bugType->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $bugType =  BugType::find($id);
        $key = BugType::where('key','!=', 0)->lists('name','id')->all();
        $source = config::get('constant.stream_types');
        return view('bug_type.edit', compact('bugType', 'key','source'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBugTypeRequest $request, $id)
    {
        $bugType = BugType::find($id);
        $bugType->name = $request->name;
        $bugType->related_id = $request->related_id;
        $bugType->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
