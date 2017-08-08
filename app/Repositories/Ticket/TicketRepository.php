<?php
namespace App\Repositories\Ticket;

use App\Models\Ticket;
use DateTime;
use App\Repositories\Status\StatusRepositoryInterface;
use App\Repositories\Priority\PriorityRepositoryInterface;
use App\Repositories\BugWeight\BugWeightRepositoryInterface;
use App\Repositories\BugType\BugTypeRepositoryInterface;
use App\Repositories\RootCause\RootCauseRepositoryInterface;
use App\Repositories\TicketType\TicketTypeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use DB;

/**
 *
 * Dec 23, 2016 4:07:45 PM
 * @author tampt6722
 *
 */
class TicketRepository implements TicketRepositoryInterface
{

    public function __construct(TicketTypeRepositoryInterface $tk,
                                StatusRepositoryInterface $status,
                                PriorityRepositoryInterface $priority,
                                BugTypeRepositoryInterface $bugType,
                                BugWeightRepositoryInterface $bugWeight,
                                RootCauseRepositoryInterface $rootCause)
    {
        $this->ticketType = $tk;
        $this->status = $status;
        $this->priority = $priority;
        $this->bugType = $bugType;
        $this->bugWeight = $bugWeight;
        $this->rootCause = $rootCause;
    }

    /**
     * @todo Get all ticket
     * @see \App\Repositories\Ticket\TicketRepositoryInterface::all()
     */
    public function all(){
        return Ticket::all();
    }

    /**
     * @todo Paginate ticket with number ticket in a page
     *
     * @param int $quantity
     * @see \App\Repositories\Ticket\TicketRepositoryInterface::paginate()
     */
    public function paginate($quantity){
        return Ticket::paginate($quantity);
    }

    /**
     * @todo Paginate ticket with number ticket in a page
     *
     * @param int $id
     * @see \App\Repositories\Ticket\TicketRepositoryInterface::find()
     */
    public function find($id){
        return Ticket::find($id);
    }

    /**
     * @todo Save ticket (If a value in data is null, set default data for it)
     *
     * @param array $data
     * @see \App\Repositories\Ticket\TicketRepositoryInterface::save()
     */
    public function save($data) {
        $ticket = new Ticket();
        $ticket->integrated_ticket_id = $data['integrated_ticket_id'];
        $ticket->source_id= $data['source_id'];

        if (isset($data['integrated_parent_id'])) {
            $ticket->integrated_parent_id= $data['integrated_parent_id'];
        } else {
            $ticket->integrated_parent_id = 0;
        }

        if (isset($data['ticket_type_id'])) {
            $ticket->ticket_type_id= $data['ticket_type_id'];
        } else {
            $ticket->ticket_type_id = $this->ticketType->getTicketTypeIdDefault();
        }

        if (isset($data['status_id'])) {
            $ticket->status_id= $data['status_id'];
        } else {
            $ticket->status_id = $this->status->getStatusIdDefault();
        }

        if (isset($data['description'])) {
            $ticket->description = $data['description'];
        } else {
            $ticket->description = "";
        }

        if (isset($data['title'])) {
            $ticket->title = $data['title'];
        } else {
            $ticket->title = "";
        }

        if (isset($data['category'])) {
            $ticket->category= $data['category'];
        } else {
            $ticket->category = "";
        }

        if (isset($data['version_id'])) {
            $ticket->version_id = $data['version_id'];
        } else {
            $ticket->version_id = 0;
        }

        if (isset($data['estimate_time'])) {
            $ticket->estimate_time = $data['estimate_time'];
        } else {
            $ticket->estimate_time = 0;
        }

        if (isset($data['start_date'])) {
            $ticket->start_date = $data['start_date'];
        } else {
            $ticket->start_date = "";
        }

        if (isset($data['due_date'])) {
            $ticket->due_date = $data['due_date'];
        } else {
            $ticket->due_date = "";
        }

        if (isset($data['progress'])) {
            $ticket->progress = $data['progress'];
        } else {
            $ticket->progress = "";
        }

        if (isset($data['completed_date'])) {
            $ticket->completed_date = $data['completed_date'];
        } else {
            $ticket->completed_date = "";
        }

        if (isset($data['created_by_user'])) {
            $ticket->created_by_user = $data['created_by_user'];
        } else {
            $ticket->created_by_user = '';
        }

        if (isset($data['assign_to_user'])) {
            $ticket->assign_to_user = $data['assign_to_user'];
        } else {
            $ticket->assign_to_user = '';
        }

        if (isset($data['made_by_user'])) {
            $ticket->made_by_user= $data['made_by_user'];
        } else {
            $ticket->made_by_user = '';
        }

        if (isset($data['integrated_created_at'])) {
            $ticket->integrated_created_at= $data['integrated_created_at'];
        } else {
            $ticket->integrated_created_at = "";
        }
        if (isset($data['integrated_updated_at'])) {
            $ticket->integrated_updated_at= $data['integrated_updated_at'];
        } else {
            $ticket->integrated_updated_at = "";
        }

        if (isset($data['bug_type_id'])) {
            $ticket->bug_type_id = $data['bug_type_id'];
        } else {
            $ticket->bug_type_id = $this->bugType->getBugTypeIdDefault();
        }

        if (isset($data['priority_id'])) {
            $ticket->priority_id= $data['priority_id'];
        } else {
            $ticket->priority_id = $this->priority->getPriorityIdDefault();
        }

        if (isset($data['root_cause_id'])) {
            $ticket->root_cause_id= $data['root_cause_id'];
        } else {
            $ticket->root_cause_id = $this->rootCause->getRootCauseIdDefault();
        }

        if (isset($data['impact_analysis'])) {
            $ticket->impact_analysis= $data['impact_analysis'];
        } else {
            $ticket->impact_analysis = "";
        }

        if (isset($data['bug_weight_id'])) {
            $ticket->bug_weight_id= $data['bug_weight_id'];
        } else {
            $ticket->bug_weight_id = $this->bugWeight->getBugWeightIdDefault();
        }

        if (isset($data['test_case'])) {
            $ticket->test_case = $data['test_case'];
        } else {
            $ticket->test_case = 0;
        }

        $ticket->project_id= $data['project_id'];

        $ticket->save();
        return $ticket->id;
    }

    /**
     * @todo Delete ticket
     *
     * @param int $id
     * @see \App\Repositories\Ticket\TicketRepositoryInterface::delete()
     */
    public function delete($id){
        Ticket::find($id)->delete();
    }

    /**
     * @todo Update ticket daily if it has not yet close
     *
     * @author tampt6722
     * @param array $data
     * @param int $id
     * @see \App\Repositories\Ticket\TicketRepositoryInterface::update()
     */
    public function update($data, $id){
        $ticket = Ticket::find($id);

        if (isset($data['integrated_ticket_id'])) {
             $ticket->integrated_ticket_id = $data['integrated_ticket_id'];
        }
        if (isset($data['integrated_parent_id'])) {
            $ticket->integrated_parent_id= $data['integrated_parent_id'];
        }

        if (isset($data['source_id'])) {
            $ticket->source_id= $data['source_id'];
        }

        if (isset($data['ticket_type_id'])) {
            $ticket->ticket_type_id= $data['ticket_type_id'];
        }
        if (isset($data['title'])) {
            $ticket->title = $data['title'];
        }
        if (isset($data['status_id'])) {
            $ticket->status_id= $data['status_id'];
        }

        if (isset($data['description'])) {
            $ticket->description = $data['description'];
        }

        if (isset($data['category'])) {
            $ticket->category= $data['category'];
        }

        if (isset($data['version_id'])) {
            $ticket->version_id = $data['version_id'];
        }

        if (isset($data['estimate_time'])) {
            $ticket->estimate_time = $data['estimate_time'];
        }

        if (isset($data['start_date'])) {
            $ticket->start_date = $data['start_date'];
        }

        if (isset($data['due_date'])) {
            $ticket->due_date = $data['due_date'];
        }

        if (isset($data['progress'])) {
            $ticket->progress = $data['progress'];
        }

        if (isset($data['completed_date'])) {
            $ticket->completed_date = $data['completed_date'];
        }

        if (isset($data['created_by_user'])) {
            $ticket->created_by_user = $data['created_by_user'];
        }

        if (isset($data['assign_to_user'])) {
            $ticket->assign_to_user = $data['assign_to_user'];
        }

        if (isset($data['made_by_user'])) {
            $ticket->made_by_user= $data['made_by_user'];
        }
        if (isset($data['bug_type_email'])) {
            $ticket->bug_type_email = $data['bug_type_email'];
        }

        if (isset($data['priority_id'])) {
            $ticket->priority_id= $data['priority_id'];
        }

        if (isset($data['root_cause_id'])) {
            $ticket->root_cause_id= $data['root_cause_id'];
        }

        if (isset($data['bug_weight_id'])) {
            $ticket->bug_weight_id= $data['bug_weight_id'];
        }

        if (isset($data['test_case'])) {
            $ticket->test_case = $data['test_case'];
        }

        if (isset($data['integrated_updated_at'])) {
            $ticket->integrated_updated_at= $data['integrated_updated_at'];
        }
        if (isset($data['integrated_created_at'])) {
            $ticket->integrated_created_at= $data['integrated_created_at'];
        }

        if (isset($data['impact_analysis'])) {
            $ticket->impact_analysis= $data['impact_analysis'];
        }
        $ticket->save();
        return $ticket->id;
    }

    /**
     * @todo Find ticket by a attribute
     *
     * @author tampt6722
     * @param string $att
     * @param string $name
     * @see \App\Repositories\Ticket\TicketRepositoryInterface::findByAttribute()
     */
    public function findByAttribute($att, $name)
    {
        return Ticket::where($att, $name)->first();
    }

    /**
     * @todo Find ticket by 2 attributes
     *
     * @author tampt6722
     * @param string $att1
     * @param string $name1
     * @param string $att2
     * @param string $name3
     * @see \App\Repositories\Ticket\TicketRepositoryInterface::findByAttributes()
     */
    public function findByAttributes($att1, $name1, $att2, $name2)
    {
        return Ticket::where($att1, $name1)
        ->where($att2,$name2)->first();
    }

    /**
     *
     * @author tampt6722
     *
     * @param array $vars
     */
    public function findByManyAttributes( $vars){
        $query = DB::table('tickets');
        foreach ($vars as $key => $value) {
            $query->where($key, '=', $value);
        }

        return $query->first();
    }

    /**
     * @todo Get tickets in project
     *
     * @author chaunm8181
     * @param int $project_id
     * @param int $page
     * @param array $request
     * @see \App\Repositories\Ticket\TicketRepositoryInterface::ticket_project()
     */
    public function ticket_project($project_id, $page, $request)
    {
        $list_plan = Ticket::selectRaw('tickets.category,sum(tickets.estimate_time) as count_estimate')
                        ->where('tickets.project_id','=',$project_id)
                        ->groupBy('tickets.category')
                        ->orderBy('tickets.category', 'desc')
                        ->get();
        $list_actual = Ticket::selectRaw('tickets.category,sum(entries.actual_hour) as count_actual')
                        ->join('entries','entries.ticket_id','=','tickets.id')
                        ->where('tickets.project_id','=',$project_id)
                        ->groupBy('tickets.category')
                        ->orderBy('tickets.category', 'desc')
                        ->get();
        $perPage = 10; // Number of items per page
        $offset = ($page * $perPage) - $perPage;
        $result = new LengthAwarePaginator(
                    $list_plan->forPage($page, $perPage),
                    count($list_plan), // Total items
                    $perPage, // Items per page
                    $page, // Current page
                    ['path' => $request->url(), 'query' => $request->query()]
                );
        return ['list_plan' => $result,'list_actual' => $list_actual];
    }
}