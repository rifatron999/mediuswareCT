<?php
namespace App\Repositories;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Models\User;

use Carbon\Carbon;

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
		$data['date'] = Carbon::now()->format("Y-m-d H:i:s");

		$user = User::where('id',auth()->user()->id)->first();
		$balance = $user['balance'] + $data['amount'];
        $user->update( ['balance' => $balance] );
		return Transaction::create($data);
	}

	public function getWithdrawal(){
        $transactions = Transaction::where('user_id',auth()->user()->id)
        	->where('transaction_type',"withdraw")
        	->get();
		return $transactions;
	}

	public function createWithdrawal($data){
		$data['user_id'] = auth()->user()->id;
		$data['transaction_type'] = 'withdraw';
		$data['date'] = Carbon::now()->format("Y-m-d H:i:s");
		$data['fee'] = $thisMonthwithdrawAmount = $rate = 0;
		$isFriday = Carbon::now()->isFriday();
		$thisMonthwithdraw = $this->getWithdrawal();

		foreach($thisMonthwithdraw as $key => $tmw){
			if( carbon::create($tmw['date'])->isCurrentMonth() ){
				$thisMonthwithdrawAmount = $tmw['amount'] + $thisMonthwithdrawAmount;
			}
		}

		if(auth()->user()->account_type == 'individual' && !$isFriday){
			$rate = 0.015;
			//dd( (double)$data['amount'] );
			if( $thisMonthwithdrawAmount > 5000 && $data['amount'] > 1000 ){
				$data['fee'] = ($data['amount'] - 1000) * $rate ;
			}
		}elseif(auth()->user()->account_type == 'business'){
			$rate = 0.025;
			if( $thisMonthwithdrawAmount > 50000 ){
				$rate = 0.015;
			}
			$data['fee'] = $data['amount'] * $rate ;
		}

		//dd('w',$thisMonthwithdrawAmount,$data['fee'],auth()->user()->account_type);
		

		$user = User::where('id',auth()->user()->id)->first();
		$balance = $user['balance'] - ( $data['amount'] + $data['fee'] );
        $user->update( ['balance' => $balance] );
		return Transaction::create($data);
	}
}