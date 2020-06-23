<?php
namespace Aiman\ContentManager\Http\Controllers;


use Aiman\ContentManager\Http\Models\ContentPanel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Http\Requests\NovaRequest;

class ContentPanelController extends Controller
{

    public function create(NovaRequest $request)
    {
        try{
            $name = $request->input('name');
            $status = $request->input('status');
            $order = $request->input('order');

            ContentPanel::query()->create([
                'name' => $name,
                'status' => $status,
                'order' => $order,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Panel Created',
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
            $content_panels = ContentPanel::query()->orderBy('order')->get();

            return response()->json([
                'status' => true,
                'message' => 'Panels Fetched',
                'data' => $content_panels,
            ]);
        }catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function updateStatus(NovaRequest $request)
    {
        try {
            $panel_id = $request->input('panel_id');
            $status = $request->input('status');
            ContentPanel::query()->where('id', $panel_id)->update([
                'status' => $status,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Panel Status updated'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function updateName(NovaRequest $request)
    {
        try{
            $panel_id = $request->input('panel_id');
            $name = $request->input('name');

            ContentPanel::query()->where('id', $panel_id)->update([
                'name' => $name,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Panel Name updated'
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
            $panels = $request->input('panels');

            $count = 1;
            foreach ($panels as $panel){
                ContentPanel::query()->updateOrCreate([
                    'id' => $panel['id']
                ],[
                    'order' => $count,
                ]);
                $count++;
            }
            return response()->json([
                'status' => true,
                'message' => 'Panel Sorted'
            ]);
        }catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }

    }
//
    public function delete($panel_id)
    {
        try{
            ContentPanel::query()->where('id', intval($panel_id))->delete();
            return response()->json([
                'status' => true,
                'message' => 'Panel Deleted',
            ]);
        }catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
