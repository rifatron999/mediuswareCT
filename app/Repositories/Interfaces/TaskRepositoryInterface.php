<?php
namespace App\Repositories\Interfaces;

Interface TaskRepositoryInterface{
	
	public function getTasks($data);
	public function createTask($data);
	public function updateTask($data,$id);
}