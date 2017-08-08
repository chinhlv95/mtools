<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\TicketType\TicketTypeRepositoryInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class TicketTypeController extends Controller
{

    public function __construct(TicketTypeRepositoryInterface $ticket_type) {
        $this->ticket_type = $ticket_type;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ticket_type = $this->ticket_type->all();
        return view('ticket_type.index', compact('ticket_type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $source_id = config::get('constant.stream_types');
        return view('ticket_type.create', compact('source_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ticket_type = $this->ticket_type->save($request->except('_token'));
        return redirect(Route('ticket_type.index'))
            ->withSuccess(Lang::get('message.create_ticket_type_success'));
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
