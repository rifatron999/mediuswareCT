<?php
namespace App\Repositories;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Models\User;


class TransactionRepository implements TransactionRepositoryInterface{

	public function getTransactions(){
		$transactions = Transaction::where('user_id',auth()->user()->id)
        	->get();
		return $transactions;
	}

	public function getDeposit(){
        $transactions = Transaction::where('user_id',auth()->user()->id)
        	->where('transaction_type',"deposit")
        	->get();
		return $transactions;
	}

	public function createDeposit($data){
		//dd($data);
		$data['user_id'] = auth()->user()->id;
		$data['transaction_type'] = 'deposit';
		$data['date'] = date("Y-m-d H:i:s");

		$user = User::where('id',auth()->user()->id)->first();
		$balance = $user['balance'] + $data['amount'];
        $user->update( ['balance' => $balance] );
		return Transaction::create($data);
	}
}