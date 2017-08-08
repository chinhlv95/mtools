<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $repositories = [
            'User\UserRepositoryInterface' => 'User\UserRepository',
            'Crawler\CrawlerTypeRepositoryInterface' => 'Crawler\CrawlerTypeRepository',
            'Crawler\CrawlerUrlRepositoryInterface' => 'Crawler\CrawlerUrlRepository',
            'Project\ProjectRepositoryInterface' => 'Project\ProjectRepository',
            'ProjectMember\ProjectMemberRepositoryInterface' => 'ProjectMember\ProjectMemberRepository',
            'Member\MemberRepositoryInterface' => 'Member\MemberRepository',
            'ProjectRisk\ProjectRiskRepositoryInterface' => 'ProjectRisk\ProjectRiskRepository',
            'ProjectKpt\ProjectKptRepositoryInterface' => 'ProjectKpt\ProjectKptRepository',
            'Categories\CategoriesRepositoryInterface' => 'Categories\CategoriesRepository',
            'ProjectReleases\ProjectReleaseRepositoryInterface' => 'ProjectReleases\ProjectReleaseRepository',
            'Ticket\TicketRepositoryInterface' => 'Ticket\TicketRepository',
            'TicketType\TicketTypeRepositoryInterface' => 'TicketType\TicketTypeRepository',
            'Entry\EntryRepositoryInterface' => 'Entry\EntryRepository',
            'ProjectVersion\ProjectVersionRepositoryInterface' => 'ProjectVersion\ProjectVersionRepository',
            'Api\ApiRepositoryInterface' => 'Api\ApiRepository',
            'Activity\ActivityRepositoryInterface' => 'Activity\ActivityRepository',
            'Import\ImportRepositoryInterface' => 'Import\ImportRepository',
            'Status\StatusRepositoryInterface' => 'Status\StatusRepository',
            'FileUpload\FileUploadRepositoryInterface' => 'FileUpload\FileUploadRepository',
            'BugWeight\BugWeightRepositoryInterface' => 'BugWeight\BugWeightRepository',
            'BugType\BugTypeRepositoryInterface' => 'BugType\BugTypeRepository',
            'Priority\PriorityRepositoryInterface' => 'Priority\PriorityRepository',
            'RootCause\RootCauseRepositoryInterface' => 'RootCause\RootCauseRepository',
            'RoleUsers\RoleUsersRepositoryInterface' => 'RoleUsers\RoleUsersRepository',
            'Department\DepartmentRepositoryInterface' => 'Department\DepartmentRepository',
            'ContentManagement\ContentManagementRepositoryInterface' => 'ContentManagement\ContentManagementRepository',
            'QualityReport\QualityReportByProjectRepositoryInterface' => 'QualityReport\QualityReportByProjectRepository',
            'QualityReport\QualityReportByPmRepositoryInterface' => 'QualityReport\QualityReportByPmRepository',
            'QualityReport\QualityReportByMemberRepositoryInterface' => 'QualityReport\QualityReportByMemberRepository',
            'MemberProjectReport\MemberProjectReportRepositoryInterface' => 'MemberProjectReport\MemberProjectReportRepository',
            'Loc\LocRepositoryInterface' => 'Loc\LocRepository',
            'ProjectKpi\ProjectKpiRepositoryInterface' => 'ProjectKpi\ProjectKpiRepository',
            'Permission\PermissionRepositoryInterface' => 'Permission\PermissionRepository',
            'MemberReport\MemberReportRepositoryInterface' => 'MemberReport\MemberReportRepository',
            'ProjectReport\ProjectReportRepositoryInterface' => 'ProjectReport\ProjectReportRepository',
            'Ranking\RankingRepositoryInterface' => 'Ranking\RankingRepository',
            'LogProject\LogProjectRepositoryInterface' => 'LogProject\LogProjectRepository',
        ];
        foreach ($repositories as $key=>$val){
            $this->app->bind("App\\Repositories\\$key", "App\\Repositories\\$val");
        }
    }
}
