<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddRequest;
use App\Models\Task;
use App\Models\Projects;
use Validator;




class TaskController extends Controller
{
    public function Index(){

        $projects = Projects::orderBy('id', 'DESC')->get();
        
        return view('task.list',compact('projects'));
    }
    public function Data(Request $request){
      
        $task_list = Task::join('projects','projects.id','task.project')
                         ->Filter($request->project) // filter by project
                         ->Search($request->search['value']) // general-search
                         ->select('task.id','task.name','projects.name as project','task.created_at')
                         ->orderBy('task.priority', 'DESC');

        $total = $task_list->count();

        if ($request->length == -1) {
            $result['data'] = $task_list->get();
        } else {
            $result['data'] = $task_list->take($request->length)->skip($request->start)->get();
        }

        $result['recordsTotal'] = $total;
        $result['recordsFiltered'] =  $total;

        echo json_encode($result);
    }
    public function Create(){

        $projects = Projects::orderBy('id', 'DESC')->get();

        return view('task.create',compact('projects'));
    }
    public function Store(AddRequest $request){

        $data = $request->only('name','project');

        $data['priority'] = Task::max('id')+1;
        
        Task::create($data);

        return response()->json(['status'=>true,'message' => 'Task Added Sucessfully..!']);
    }
    public function Show($id){

        $task = Task::select('name','project')->findOrFail($id);

        $projects = Projects::orderBy('id', 'DESC')->get();

        return view('task.edit',compact('projects','task','id'));
    }
    public function Update(AddRequest $request,$id){
        
        $task = Task::findOrFail($id);
        
        $data = $request->only('name','project');

        $task->fill($data)->save();
        
        return response()->json(['status'=>true,'message' => 'Task Updated Sucessfully..!']);
    }
    public function Destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(['status'=>true,'message' => 'Task Deleted Sucessfully..!']);
    }

    public function PriorityUpdate(Request $request){

        $i = 1;
        foreach ($request->item as $value) {
            $sort_pin = Task::whereId($value)->update(['priority' => $i]);
            $i++;
        }

        return response()->json(['status'=>true,'message' => 'Priority Updated Sucessfully..!']);
    }
    


}

?>
