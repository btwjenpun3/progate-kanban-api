<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource as ResourcesTaskResource;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskFile;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index() {

        $tasks = Task::all();

        if($tasks) {
            return response()->json([
                'code' => 200,
                'message' => 'All tasks retrieved successfully',
                'data' => TaskResource::collection(Task::all())
            ], 200);
        }
        
        return response()->json([
            'message' => 'Unknown error. Please contact administrator'
        ], 401);
    }

    public function indexById($id) {

        $task = Task::find($id);

        if($task){
            return response()->json([
                'code' => 200,
                'message' => 'Task ID ' . $task->id . ' successfully retrieved',
                'data' => $task
            ]);
        }

        return response()->json([
            'code' => 404,
            'message' => 'Task not found'
        ], 404);
    }

    public function store(Request $request)
    {        
        $request->validate(
            [
                'name' => 'required',
                'due_date' => 'required',
                'status' => 'required',
                'file' => ['max:5000', 'mimes:pdf,jpeg,png'],
            ], 
            [
                'file.max' => 'The file size exceed 5 mb',
                'file.mimes' => 'Must be a file of type: pdf,jpeg,png',
            ],
            $request->all()
        );       

        DB::beginTransaction();
        try {
           $task = Task::create([
                'name' => $request->name,
                'detail' => $request->detail,
                'due_date' => $request->due_date,
                'status' => $request->status,
                'user_id' => Auth::user()->id,
                'file' => $request->file
            ]);

            $file = $request->file('file');
            if ($file) {
                $filename = $file->getClientOriginalName();
                $path = $file->storePubliclyAs(
                    'tasks',
                    $file->hashName(),
                    'public'
                );

                TaskFile::create([
                    'task_id' => $task->id,
                    'filename' => $filename,
                    'path' => $path,
                ]);
            }

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Task successfully stored',
                'data' => [
                    'name' => $request->name,
                    'detail' => $request->detail,
                    'due_date' => $request->due_date,
                    'status' => $request->status,
                    'user_id' => Auth::user()->id,
                    'file' => $filename
                ]
            ], 200);

        } catch (\Throwable $th) {

            DB::rollBack();

            return response()->json([
                'code' => 401,
                'mesasage' => 'Task stored failed' 
            ], 401);
        }

        return response()->json([
            'code' => 401,
            'message' => 'Uknown error. Please contact administrator'
        ]);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);        

        $task->update([            
            'name' => $request->name,
            'detail' => $request->detail,
            'due_date' => $request->due_date,
            'status' => $request->status
        ]);
        
        return response()->json([
            'code' => 200,
            'message' => 'Task ID ' . $task->id . ' successfully edited',
            'data' => [
                'name' => $request->name,
                'detail' => $request->detail,
                'due_date' => $request->due_date,
                'status' => $request->status
            ]
        ], 200);
    }

    public function move(int $id, Request $request)
    {
        $task = Task::findOrFail($id);        

        $task->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'code' => 200,
            'message' => 'Task ' . $task->id .' successfully moved'
        ]);
    }

    public function destroy($id)
    {
        $task = Task::find($id);        

        foreach($task->files as $file){

            Storage::disk('public')->delete($file->path);
            
            $file->delete();
        }      

        $task->delete();
        
        return response()->json([
            'code' => 200,
            'message' => 'Task ID ' . $task->id . ' successfully deleted'
        ]);
    } 
}
