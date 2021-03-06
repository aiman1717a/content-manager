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
            $panel_id = $request->input('panel_id');
            $this->model = app($model);

            $this->model->query()->create([
                'order' => $order,
                'panel_id' => $panel_id
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
            $panel_id = $request->input('panel_id');
            $this->model = app($model);

            $contents = $this->model->where('panel_id', $panel_id)->orderBy('order')->get();

            return response()->json([
                'status' => true,
                'message' => 'Contents Fetched',
                'data' => $contents,
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
            $this->model = app($model);

            $this->model->query()->where('id', $content['id'])->update([
                'panel_id' => $content['panel_id'],
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
            $this->model = app( $model);

            $count = 1;
            foreach ($contents as $content){
                $this->model->query()->updateOrCreate([
                    'id' => $content['id']
                ],[
                    'order' => $count,
                    'panel_id' => $content['panel_id'],
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
            $this->model = app($model);

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
