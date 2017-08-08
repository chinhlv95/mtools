<?php
namespace App\Repositories\TicketType;

use App\Models\TicketType;

/**
 *
 * @author tampt6722
 *
 */
class TicketTypeRepository implements TicketTypeRepositoryInterface
{

    public function all(){
        return TicketType::all();
    }

    public function paginate($quantity){
        return TicketType::paginate($quantity);
    }

    public function find($id){
        return TicketType::find($id);
    }

    /**
     * Save ticket type data
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\TicketType\TicketTypeRepositoryInterface::save()
     */
    public function save($data){
        $ticket = new TicketType();
        $ticket->name= $data['name'];
        $ticket->source_id= $data['source_id'];
        $ticket->integrated_ticket_type_id= $data['integrated_ticket_type_id'];
        $ticket->save($data);
        return $ticket->id;
    }

    public function delete($id){
        TicketType::find($id)->delete();
    }

    public function update($data, $id){
        $ticket = TicketType::find($id);
        // save project
        $ticket->save();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\TicketType\TicketTypeRepositoryInterface::findByAttribute()
     */
    public function findByAttribute($att, $name){
        return TicketType::where($att, $name)->first();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\TicketType\TicketTypeRepositoryInterface::findByAttributes()
     */
    public function findByAttributes($att1, $name1, $att2, $name2){
        return TicketType::where($att1, $name1)
                        ->where($att2,$name2)->first();
    }

   /**
    *
    * {@inheritDoc}
    * @see \App\Repositories\TicketType\TicketTypeRepositoryInterface::getTicketTypeWithKey()
    */
    public function getTicketTypeWithKey($key) {
        $id = TicketType::where('key', $key)->first()->id;
        $query = TicketType::where('related_id', $id)->get();

        return $query;
    }

    /**
     * Get the id of the default ticket type
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\TicketType\TicketTypeRepositoryInterface::getTicketTypeIdDefault()
     */
    public function getTicketTypeIdDefault() {
        $ticketType = $this->findByAttributes('source_id', 0, 'key', 12);
        return $ticketType->id;
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\TicketType\TicketTypeRepositoryInterface::getTicketTypeId()
     */
    public function getTicketTypeId($integratedTicketTypeId, $name, $sourceId)
    {
        $existedTicketType = $this->findByAttributes( 'name',
                $name, 'source_id', $sourceId );
        if (empty($existedTicketType)) {
            $dataTicketType ['name'] = $name;
            $dataTicketType ['source_id'] = $sourceId;
            $dataTicketType ['integrated_ticket_type_id'] = $integratedTicketTypeId;
            $ticketTypeId = $this->save( $dataTicketType );
        } else {
            $ticketTypeId = $existedTicketType->id;
        }

        return $ticketTypeId;
    }

}