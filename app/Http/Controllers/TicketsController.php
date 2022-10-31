<?php

namespace App\Http\Controllers;

use App\Repositories\TicketsIndexQuery;
use App\Repositories\TicketsRepository;
use App\Ticket;
use BadChoice\Thrust\Controllers\ThrustController;

class TicketsController extends Controller
{
    public function index()
    {
        return (new ThrustController)->index('tickets');
    }

    /*public function index(TicketsRepository $repository)
    {
        $ticketsQuery = TicketsIndexQuery::get($repository);
        $ticketsQuery = $ticketsQuery->select('tickets.*')->latest('updated_at');

        return view('tickets.index', ['tickets' => $ticketsQuery->paginate(25, ['tickets.user_id'])]);
    }*/

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        return view('tickets.show', ['ticket' => $ticket]);
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store()
    {
        $this->validate(request(), [
            'requester' => 'required|array',
            'title'     => 'required|min:3',
            'body'      => 'required',
            'team_id'   => 'nullable|exists:teams,id',
            'invoice_number' => 'required',
            'uuid' => 'required',
            'load_number' => 'required',
            'order_number' => 'required',
            'cust_ref' => 'required',
            'total' => 'required',
            'coin_type' => 'required',
            'error_type' => 'required',
        ]);
        $ticket = Ticket::createAndNotify(request('requester'), request('title'), request('body'), request('tags'),
            request('invoice_number'), 
            request('uuid'),
            request('load_number'),
            request('order_number'),
            request('cust_ref'),
            request('total'),
            request('coin_type'),
            request('error_type'));
        $ticket->updateStatus(request('status'));

        if (request('team_id')) {
            $ticket->assignToTeam(request('team_id'));
        }

        return redirect()->route('tickets.show', $ticket);
    }

    public function reopen(Ticket $ticket)
    {
        $ticket->updateStatus(Ticket::STATUS_OPEN);

        return back();
    }

    public function update(Ticket $ticket)
    {
        $this->validate(request(), [
            'requester' => 'required|array',
            'priority'  => 'required|integer',
            'type'      => 'integer',
            //'subject'   => 'string|nullable',
            //'summary'   => 'string'
            //'title'      => 'required|min:3',
        ]);
        $ticket->updateWith(request('requester'), request('priority'), request('type'))
                ->updateSummary(request('subject'), request('summary'));

        return back();
    }
}
