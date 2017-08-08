<?php
namespace App\Repositories\ProjectVersion;

use App\Models\ProjectVersion;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectVersionRepository implements ProjectVersionRepositoryInterface
{

    public function all(){
        return ProjectVersion::all();
    }

    public function paginate($quantity){
        return ProjectVersion::paginate($quantity);
    }

    public function find($id){
        return ProjectVersion::find($id);
    }
/**
 * Save ProjectVersion data
 *
 * @author tampt6722
 * {@inheritDoc}
 * @see \App\Repositories\ProjectVersion\ProjectVersionRepositoryInterface::save()
 */
    public function save($data){
        $projectVersion = new ProjectVersion();
        $projectVersion->integrated_version_id = $data['integrated_version_id'];
        if (isset($data['name'])) {
            $projectVersion->name = $data['name'];
        } else {
            $projectVersion->name = "";
        }
        if (isset($data['project_id'])) {
            $projectVersion->project_id = $data['project_id'];
        } else {
            $projectVersion->project_id = 0;
        }

        if (isset($data['source_id'])) {
            $projectVersion->source_id = $data['source_id'];
        } else {
            $projectVersion->source_id = 0;
        }

        if (isset($data['status'])) {
            $projectVersion->status = $data['status'];
        } else {
            $projectVersion->status = 0;
        }

        if (isset($data['description'])) {
            $projectVersion->description = $data['description'];
        } else {
            $projectVersion->description = "";
        }

        if (isset($data['start_date'])) {
            $projectVersion->start_date = $data['start_date'];
        } else {
            $projectVersion->start_date = "";
        }

        if (isset($data['end_date'])) {
            $projectVersion->end_date = $data['end_date'];
        } else {
            $projectVersion->end_date = "";
        }
        $projectVersion->save();
        return $projectVersion->id;
    }

    public function delete($id){
        ProjectVersion::find($id)->delete();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectVersion\ProjectVersionRepositoryInterface::update()
     */
    public function update($data, $id){
        $projectVersion = ProjectVersion::find($id);
        if (isset($data['name'])) {
            $projectVersion->name = $data['name'];
        }
        if (isset($data['description'])) {
            $projectVersion->description = $data['description'];
        }
        if (isset($data['start_date'])) {
            $projectVersion->start_date = $data['start_date'];
        }
        if (isset($data['end_date'])) {
            $projectVersion->end_date = $data['end_date'];
        }
        if (isset($data['status'])) {
            $projectVersion->status = $data['status'];
        }
        $projectVersion->save();
        return true;
    }


    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectVersion\ProjectVersionRepositoryInterface::findByAttribute()
     */
    public function findByAttribute($att, $name){
        return ProjectVersion::where($att, $name)->first();
    }

    public function getVersionByAttribute($attribute, $name) {
        return ProjectVersion::where($attribute, $name)->get();
    }
    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\ProjectVersion\ProjectVersionRepositoryInterface::findByAttributes()
     */
    public function findByAttributes($att1, $name1, $att2, $name2){
        return ProjectVersion::where($att1, $name1)
                             ->where($att2,$name2)->first();
    }

    /**
     * @author chaunm8181
     * {@inheritDoc}
     * @see \App\Repositories\ProjectVersion\ProjectVersionRepositoryInterface::getDataJoinTicketAndVersion()
     */
    public function getDataJoinTicketAndVersion(){
        return $query = ProjectVersion::select(
                    'project_versions.id as version_id',
                    'tickets.id as ticket_id',
                    'tickets.estimate_time')
                    ->join('tickets','tickets.version_id','=','project_versions.id')
                    ->where('tickets.deleted_at', null);
    }

    /**
     * @todo Get data of version related tickets and entries
     *
     * @author chaunm8181
     * @see \App\Repositories\ProjectVersion\ProjectVersionRepositoryInterface::getDataJoinTicketAndEntries()
     */
    public function getDataJoinTicketAndEntries()
    {
        return $query = ProjectVersion::select(
                    'project_versions.id as version_id',
                    'tickets.id as ticket_id',
                    'entries.id as entry_id',
                    'entries.actual_hour')
                    ->join('tickets','tickets.version_id','=','project_versions.id')
                    ->join('entries','entries.ticket_id', '=' ,'tickets.id')
                    ->where('tickets.deleted_at', null)
                    ->where('entries.deleted_at', null);
    }

    public function getDataVersionAndEntriesTicket($project_id, $page, $request)
    {
        $versionEstimate   =  ProjectVersion::select(
                DB::raw('sum(tickets.estimate_time) AS estimate'),
                'project_versions.id as version_id',
                'project_versions.name as name',
                'project_versions.start_date',
                'project_versions.end_date',
                'tickets.id as ticket_id')
                ->leftJoin('tickets','tickets.version_id','=','project_versions.id')
                ->where('project_versions.project_id','=',$project_id)
                ->where('tickets.deleted_at', null)
                ->groupBy('project_versions.name')
                ->get()
                ;
        $versionActual = ProjectVersion::select(
                DB::raw('sum(entries.actual_hour) AS actual_hour'))
                ->leftJoin('tickets','tickets.version_id','=','project_versions.id')
                ->leftJoin('entries','entries.ticket_id','=','tickets.id')
                ->where('project_versions.project_id','=',$project_id)
                ->where('entries.deleted_at', null)
                ->groupBy('project_versions.name')
                ->get()
                ;
        $resultVersion = $this->paginateCollection(10,$page,$request,$versionEstimate);
        $resultActual = $this->paginateCollection(10,$page,$request,$versionActual);

        return ['versions' => $resultVersion,'versionActual' => $resultActual];
    }
    public function  paginateCollection($perPage,$page,$request,$collection)
    {
        $perPage = 10; // Number of items per page
        $offset = ($page * $perPage) - $perPage;
        $result = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            count($collection), // Total items
            $perPage, // Items per page
            $page, // Current page
            ['path' => $request->url(), 'query' => $request->query(),'pageName' => 'paginate-version']
        );
        return $result;
    }
    /**
     * @todo Get data of version related tickets
     *
     * @author chaunm8181
     * @see \App\Repositories\ProjectVersion\ProjectVersionRepositoryInterface::getDataTaskInVersion()
     */
    public function getDataTaskInVersion()
    {
        return $query = ProjectVersion::select(
                    'project_versions.id as version_id',
                    'tickets.id as ticket_id',
                    'tickets.ticket_type_id')
                    ->join('tickets','tickets.version_id','=','project_versions.id')
                    ->where('tickets.deleted_at', null);
    }


    /**
     * @todo Get version id
     *
     * @author tampt6722
     * @param int $inteVersionId
     * @param string $inteVersionName
     * @param int $projectId
     * @param int $sourceId
     * @return int $versionId
     */
    public function getVersionId ($inteVersionId, $inteVersionName, $projectId, $sourceId) {
        $existedVersion = $this->findByAttributes('integrated_version_id',
                                        $inteVersionId, 'source_id', $sourceId);
        if (count($existedVersion) > 0) {
            $versionId = $existedVersion->id;
        } else {
            $dataVersion ['integrated_version_id'] = $inteVersionId;
            $dataVersion ['name'] = $inteVersionName;
            $dataVersion ['project_id'] = $projectId;
            $dataVersion ['source_id'] = $sourceId;
            $versionId = $this->save( $dataVersion );
        }
        return $versionId;
    }

}