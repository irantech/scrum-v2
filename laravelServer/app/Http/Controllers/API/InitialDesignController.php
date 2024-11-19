<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInitialDesignRequest;
use App\Http\Resources\API\InitialDesign\ShowResource;
use App\Http\Resources\API\InitialDesign\StoreCollection;
use App\Http\Resources\API\InitialDesign\StoreResource;
use App\Models\ChecklistContract;
use App\Models\InitialDesign;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InitialDesignController extends Controller
{
    public function update(Request $request, ChecklistContract $checklistContract)
    {
        //-------------------------------------- unlink-----------------------------------------

        $initialDesign = $checklistContract->initialDesign()->first();
        $upload_path = env('UPLOAD_PATH', 'uploads/image');
        $logo_url = $upload_path . '/' . $initialDesign->logo;
        if (file_exists($logo_url))
            unlink($logo_url);
        $files_url = [];
        foreach (json_decode($initialDesign->file, true) as $key => $file) {
            $files_url[$key] = $upload_path . '/' . $file;
        }
        foreach ($files_url as $file_url)
        {
            if(file_exists($file_url))
                unlink($file_url);
        }


        //-------------------------------------- update-----------------------------------------

        $image_name = time() . '.' . $request->logo->extension();
        $request->logo->move($upload_path, $image_name);
        $files_arr = [];
        foreach ($request->file('files') as $key => $file) {
            $microTime = microtime(true);
            $milliseconds = round($microTime * 10000);
            $file_name = $milliseconds . '.' . $file->getClientOriginalExtension();
            $files_arr[$key] = $file_name;
            $file->move($upload_path, $file_name);
        }
        $initialDesign->update([
           'main_color' => $request->main_color,
           'second_color' => $request->second_color,
           'description' => $request->description,
            'logo'=> $image_name,
            'file' => json_encode($files_arr),
        ]);

        $initialDesignUpdate=$checklistContract->initialDesign()->first();
        $data = new StoreResource($initialDesignUpdate);
        return response()->json($data, Response::HTTP_OK);

    }

    public function show(ChecklistContract $checklistContract)
    {
        $data = new StoreResource($checklistContract->initialDesign()->first());
        return response()->json($data, Response::HTTP_OK);
    }

    public function store(StoreInitialDesignRequest $request, ChecklistContract $checklistContract)
    {
        $upload_path = env('UPLOAD_PATH', 'uploads/image');
        $image_name = time() . '.' . $request->logo->extension();
        $request->logo->move($upload_path, $image_name);
        $files_arr = [];
        foreach ($request->file('files') as $key => $file) {
            $microTime = microtime(true);
            $milliseconds = round($microTime * 10000);
            $file_name = $milliseconds . '.' . $file->getClientOriginalExtension();
            $files_arr[$key] = $file_name;
            $file->move($upload_path, $file_name);
        }
        $initialdesign = InitialDesign::create([
            'main_color' => $request->main_color,
            'second_color' => $request->second_color,
            'logo' => $image_name,
            'description' => $request->description,
            'file' => json_encode($files_arr),
            'checklist_contract_id' => $checklistContract->id,
        ]);

        $data = new StoreResource($initialdesign);
        return response()->json($data, Response::HTTP_OK);
    }


}
