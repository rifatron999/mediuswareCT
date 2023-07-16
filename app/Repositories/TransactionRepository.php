<?php
namespace App\Repositories;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Models\User;


class TransactionRepository implements TransactionRepositoryInterface{

	public function getTransactions($data){
		$transactions = Transaction::query();
        if(isset( $data['name'] )){
            $transactions = $transactions->where('name','LIKE','%'.$data['name'].'%');
        }
        $transactions = $transactions->get();

		return $transactions;
	}

	public function getDeposit(){
        $transactions = Transaction::where('user_id',auth()->user()->id)->get();
        //dd($transactions);
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