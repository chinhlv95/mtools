<?php
namespace App\Repositories\TicketType;

/**
 *
 * @author tampt6722
 *
 */
interface TicketTypeRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function delete($id);

    public function save($data);

    public function update($data, $id);

    public function findByAttribute($att, $name);

    public function findByAttributes($att1, $name1, $att2, $name2);

    public function getTicketTypeWithKey($key);

    public function getTicketTypeIdDefault();

    public function getTicketTypeId($integratedTicketTypeId, $name, $sourceId);
}