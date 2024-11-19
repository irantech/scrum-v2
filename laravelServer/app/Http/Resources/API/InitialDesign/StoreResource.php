<?php

namespace App\Http\Resources\API\InitialDesign;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'main_color' => $this->main_color,
            'second_color' => $this->second_color,
            'logo' => $this->logo,
            'logo_url' => 'http://localhost/' .env('UPLOAD_PATH', 'uploads/image').'/' . $this->logo,
            'description' => $this->description,
            'file' => $this->file,
            'file_url'=>$this->url($this->file),

            'checklist_contract_id' => $this->checklist_contract_id,
        ];
    }

    public function url($files)
    {
        $url=[];
        foreach (json_decode($files, true) as $key=>$file){
            $url[$key]= 'http://localhost/' .env('UPLOAD_PATH', 'uploads/image').'/' . $file;
        }
        return $url;
    }
}
