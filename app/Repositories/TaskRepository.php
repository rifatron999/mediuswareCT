<?php
namespace App\Repositories;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Models\Task;


class TaskRepository implements TaskRepositoryInterface{

	public function getTasks($data){
		$user_info = user_info(auth()->user()->position);
		$tasks = Task::with('project');
        if( isset($data['project']) ){
            $tasks->whereHas('project',function($query) use($data){
                $query->where('name','LIKE','%'.$data['project'].'%');
            });
        }
        if( isset($data['status']) ){
            $tasks = $tasks->where('status','LIKE','%'.$data['status'].'%');
        }
        if($user_info['is_manager']){
            if( isset($data['team_mate']) ){
                $tasks = $tasks->with('user');
                $tasks->whereHas('user',function($query) use($data){
                    $query->where('name','LIKE','%'.$data['team_mate'].'%');
                });
            }
        }

        $tasks = $tasks->where($user_info['task_search_assigned'],auth()->user()->id)->get();

		return $tasks;
	}

	public function createTask($data){
		return Task::create($data);
	}

	public function updateTask($data,$id){
		return Task::where('assigned_to',auth()->user()->id)
            ->where('id',$id)
            ->update( ['status' => $data['status']] );

	}
}