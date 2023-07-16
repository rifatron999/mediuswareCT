<?php
namespace App\Repositories\Interfaces;

Interface TransactionRepositoryInterface{
	
	public function getTransactions();
	public function getDeposit();
	public function createDeposit($data);
}