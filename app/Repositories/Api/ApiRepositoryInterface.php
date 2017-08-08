<?php
namespace App\Repositories\Api;
interface ApiRepositoryInterface
{
    public function getApi($url, $timeout);
}
