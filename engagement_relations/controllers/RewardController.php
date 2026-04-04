<?php
namespace App\Controllers;

use App\Models\Reward;

class RewardController
{
    private $reward;

    public function __construct()
    {
        $this->reward = new Reward();
    }

    public function index()
    {
        return $this->reward->all();
    }

    public function store(array $data)
    {
        return $this->reward->create($data);
    }

    public function categorizeRewards($category)
    {
        return $this->reward->getByCategory($category);
    }
}
