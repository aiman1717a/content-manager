<?php
namespace Aiman\ContentManager\Http\Controllers;


use Aiman\ContentManager\Http\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Http\Requests\NovaRequest;

class ContentController extends Controller
{
    protected $model;

    public function create(NovaRequest $request)
    {
        try{
            $model = $request->input('model');
            $order = $request->input('order');
            $type = $request->input('type');
            $this->model = app('\\' . $model);

            $this->model->query()->create([
                'order' => $order,
                'type' => $type
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Content Created',
            ]);
        }catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function reads(NovaRequest $request)
    {
        try{
            $model = $request->input('model');
            $type = $request->input('type');
            $this->model = app('\\' . $model);

            return response()->json([
                'status' => true,
                'message' => 'Contents Fetched',
                'data' => $this->model->where('type', $type)->orderBy('order')->get(),
            ]);
        }catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function update(NovaRequest $request)
    {
        try{
            $model = $request->input('model');
            $content = $request->input('content');
            $this->model = app('\\' . $model);

            $this->model->query()->where('id', $content['id'])->update([
                'type' => $content['type'],
                'article_id' => $content['article_id'],
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Content updated'
            ]);
        }catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }

    }

    public function updateSort(NovaRequest $request)
    {
        try{
            $model = $request->input('model');
            $contents = $request->input('contents');
            $this->model = app('\\' . $model);

            $count = 1;
            foreach ($contents as $content){
                $this->model->query()->updateOrCreate([
                    'id' => $content['id']
                ],[
                    'order' => $count,
                    'type' => $content['type'],
                    'article_id' => $content['article_id'],
                ]);
                $count++;
            }
            return response()->json([
                'status' => true,
                'message' => 'Content Sorted'
            ]);
        }catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }

    }

    public function delete(NovaRequest $request)
    {
        try{
            $model = $request->input('model');
            $id = $request->input('id');
            $this->model = app('\\' . $model);

            $this->model->query()->where('id', intval($id))->delete();
            return response()->json([
                'status' => true,
                'message' => 'Content Deleted',
            ]);
        }catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
