<?php
namespace App\Repositories\Interfaces;

Interface TransactionRepositoryInterface{
	
	public function getTransactions($data);
	public function getDeposit();
	public function createDeposit($data);
}