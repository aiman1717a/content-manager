<?php
namespace Aiman\ContentManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class ArticleController extends Controller
{
    protected $model;

    public function reads(NovaRequest $request)
    {
        try{
            $model = $request->input('model');
            $this->model = app($model);

            $articles = $this->model->where('status', 'PUBLISHED')->get();
            return response()->json([
                'status' => true,
                'message' => 'Articles Fetched',
                'data' => $articles
            ]);
        }catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
