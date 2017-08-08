<?php
namespace App\Repositories\Loc;

use App\Models\Loc;

class LocRepository implements LocRepositoryInterface
{

    public function all(){
        return Loc::all();
    }

    public function paginate($quantity){
        return Loc::paginate($quantity);
    }

    public function find($id){

        return Loc::find($id);
    }
    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Entry\EntryRepositoryInterface::save()
     */
    public function save($data){
        $loc = new Loc();
        $loc->project_id = $data['project_id'];
        $loc->ticket_id = $data['ticket_id'];
        $loc->loc = $data['loc'];
        if (isset($data['user_id'])) {
            $loc->user_id = $data['user_id'];
        } else {
            $loc->user_id = 0;
        }
        if (isset($data['integrated_created_at'])) {
            $loc->integrated_created_at = $data['integrated_created_at'];
        } else {
            $loc->integrated_created_at = '';
        }
        if (isset($data['integrated_updated_at'])) {
            $loc->integrated_updated_at = $data['integrated_updated_at'];
        } else {
            $loc->integrated_updated_at = '';
        }

        $loc->save();
        return $loc->id;
    }

    public function delete($id) {
        Loc::find($id)->delete();
        return true;
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Loc\LocRepositoryInterface::update()
     */
    public function update($data, $id){
        $loc = Loc::find($id);
        if (isset($data['project_id'])) {
            $loc->project_id = $data['project_id'];
        }
        if (isset($data['ticket_id'])) {
            $loc->ticket_id = $data['ticket_id'];
        }
        if (isset($data['user_id'])) {
            $loc->user_id = $data['user_id'];
        }
        if (isset($data['integrated_created_at'])) {
            $loc->integrated_created_at = $data['integrated_created_at'];
        }
        if (isset($data['integrated_updated_at'])) {
            $loc->integrated_updated_at = $data['integrated_updated_at'];
        }
        $loc->loc = $data['loc'];

        $loc->save();
        return $loc->id;
    }

    public function findByAttribute($att, $name){
        return Loc::where($att, $name)->first();
    }

    public function getDataByAttribute($att, $name){
        return Loc::where($att, $name)->get();
    }

    public function findByAttributes($att1, $name1, $att2, $name2){
        return Loc::where($att1, $name1)
                        ->where($att2,$name2)->first();
    }

    public function findByTriAttributes($att1, $name1, $att2, $name2, $att3, $name3){
        return Loc::where($att1, $name1)
                    ->where($att2,$name2)
                    ->where($att3,$name3)
                    ->first();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Loc\LocRepositoryInterface::saveloc()
     */
    public function saveloc($projectId, $ticketId, $pUserId, $loc, $integratedCreatedAt, $integratedUpdatedAt)
    {
        $checkLoc = $this->findByAttribute('ticket_id', $ticketId);
        if (count($checkLoc) == 0) {
            $locs = [
                            'project_id' => $projectId,
                            'ticket_id' => $ticketId,
                            'user_id' => $pUserId,
                            'loc' => $loc,
                            'integrated_created_at' => $integratedCreatedAt,
                            'integrated_updated_at' => $integratedUpdatedAt
            ];
            $this->save($locs);
        }
    }


    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Loc\LocRepositoryInterface::updateLoc()
     */
    public function updateLoc($projectId, $ticketId, $pUserId, $loc, $integratedCreatedAt, $integratedUpdatedAt)
    {
        $checkLoc = $this->findByAttribute('ticket_id', $ticketId);
        if (count($checkLoc) == 0) {
            $locs = [
                            'project_id' => $projectId,
                            'ticket_id' => $ticketId,
                            'user_id' => $pUserId,
                            'loc' => $loc,
                            'integrated_created_at' => $integratedCreatedAt,
                            'integrated_updated_at' => $integratedUpdatedAt
            ];
            $this->save($locs);
        } else {
            $locs = [
                            'loc' => $loc
            ];
            $this->update($locs, $checkLoc->id);
        }
    }

}