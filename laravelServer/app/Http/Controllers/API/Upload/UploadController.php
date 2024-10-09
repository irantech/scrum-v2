<?php

namespace App\Http\Controllers\API\Upload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UploadController extends Controller
{

    public function upload(Request $request) {
        $messages = array(
            'mimes'    => 'فرمت فایل انتخابی باید jpeg, png,jpg باشد.' ,
            'max'      => 'حجم فایل انتخابی باید کمتر از 500kb باشد.' ,
            'image'    => 'لطفا یک عکس انتخاب کنید.'
        );
        $validator = Validator::make($request->all(), [
            'upload' => 'image|mimes:jpg,jpeg,png,svg|max:500',
        ] , $messages);

        if ($validator->fails()) {
            return response()->json([ 'error' => [ 'message' => $validator->errors()->all() ]], Response::HTTP_OK );
        }
        $upload_path = env( 'UPLOAD_PATH' , 'uploads/image' );
        $picName = time().'.'.$request->upload->extension();
        $image = $request->upload->move( $upload_path, $picName );
//        dd(Auth::user());
//        activity()
//            ->withProperties(['image' => $picName])
//            ->log('user_upload_picture');
        return response()->json(['url' => 'https://api.ladyscarf.ir/' .$upload_path .'/' . $picName , 'picName' => $picName], Response::HTTP_OK);
    }
    public function uploadEditor(Request $request) {
        $messages = array(
            'mimes:pdf,png,jpeg,jpg'    => 'فرمت فایل انتخابی باید jpeg, png,jpg , pdf باشد.' ,
            'max'                       => 'حجم فایل انتخابی باید کمتر از 1mb باشد.' ,
        );
        $validator = Validator::make($request->all(), [
            'file' => 'mimes:jpg,jpeg,png,svg,pdf|max:1024',
        ] , $messages);

        if ($validator->fails()) {
            return response()->json([ 'error' => [ 'message' => $validator->errors()->all() ]], Response::HTTP_OK );
        }
        $upload_path = env( 'UPLOAD_PATH' , 'upload/image' );
        $picName = Str::random().'.'.$request->file->extension();
        $image = $request->file->move( $upload_path, $picName );
//
        return 'https://api.ladyscarf.ir/' .$upload_path .'/' . $picName ;
    }
}
