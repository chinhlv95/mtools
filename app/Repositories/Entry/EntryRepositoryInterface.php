<?php
namespace App\Repositories\Entry;

/**
 *
 * @author tampt6722
 *
 */
interface EntryRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function delete($id);

    public function save($data);

    public function update($data, $id);

    public function findByAttribute($att, $name);

    public function getDataByAttribute($att, $name);

    public function findByAttributes($att1, $name1, $att2, $name2);

    public function findByTriAttributes($att1, $name1, $att2, $name2, $att3, $name3);

    public function getEntryInTicket($listProjectId, $startDate, $endDate, $getReportType, $getStatus);

    public function getEntryBeforeSaveImport($spentAt, $ticketId, $projectId, $userId);

    public function updateEntryWhenImportFile($data, $id);

    public function getEntryOfPersonal($startDate, $endDate);

    public function getDataByAttributes($att1, $name1, $att2, $name2);

    public function getEntryOfPersonalWithTickets($startDate, $endDate, $listUserId);
}