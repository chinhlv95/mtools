<?php
namespace App\Repositories\Ranking;
interface RankingRepositoryInterface
{
    public function rankingDev($dataDevs);
    public function rankingQA($dataDevs);
    public function rankingProject($dataProjects);
    public function rankingBres($years,$months);
    public function rankingQAL($years,$months);
    public function rankDm($years,$months);
}