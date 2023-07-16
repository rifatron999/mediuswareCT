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
		$data['fee'] = $thisMonthwithdrawAmount = $totalwithdrawAmount = $rate = 0;
		$isFriday = Carbon::now()->isFriday();
		$totalwithdraw = $this->getWithdrawal();
		//dd($totalwithdraw);
		$user = User::where('id',auth()->user()->id)->first();
		$balance = $user['balance'];
		//
		foreach($totalwithdraw as $key => $tw){
			if( carbon::create($tw['date'])->isCurrentMonth() ){
				$thisMonthwithdrawAmount = $tw['amount'] + $thisMonthwithdrawAmount ;
			}
			$totalwithdrawAmount = $tw['amount'] + $totalwithdrawAmount;
		}

		$thisMonthwithdrawAmount = $thisMonthwithdrawAmount + $data['amount'];
		$totalwithdrawAmount = $totalwithdrawAmount + $data['amount'];

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

		$balance = $balance - ( $data['amount'] + $data['fee'] );
		
		if($balance <= 0){
			$response = [
	            'success' => false,
	            'message' => 'Insufficient Fund',
	        ];

	        return $response;
		}

		dd('withdraw amount',$data['amount'],'total mwithdraw amount'/*,$totalwithdrawAmount*/,'this month withdraw',$thisMonthwithdrawAmount,'f',$data['fee'],'b',$balance,auth()->user()->account_type);
        $user->update( ['balance' => $balance] );
		return Transaction::create($data);
	}
}