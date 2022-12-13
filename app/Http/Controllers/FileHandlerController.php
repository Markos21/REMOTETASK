<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Storage;
use App\Models\Data;
class FileHandlerController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/store/",
     *      summary="store image file",
     *      @OA\Response(
     *           name, description,type,
     *          response=201,
     *           )
     *       ),     
     */   
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' =>  'required|string|max:50',
            'description' =>  'required|string|max:250',
            'file' => 'required|mimes:jpg,png|max:5000',    
            'type' => 'required'          
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        else
        {
            $image_save_url= $request->file->store('uploads');
            
            Data::insert([
                 'name' => $request->name,
                 'description' => $request->description,
                 'file' => $image_save_url, 
                 'type'=>$request->type
             ]);

            return response()->json(
                [
                "name"=>$request->name,
                "description"=>$request->description,
                "type"=>$request->type
                ],
                201
            );
        }

    }

    /**
     * @OA\Get(
     *      path="/api/getData/",
     *      summary="get stored data",
     *      @OA\Response(
     *           name, description,type,
     *          response=200,
     *           )
     *       ),     
     */   
    public function get()
    {
        //Here we can do more pagination by passing current page in every request

        $storedItems = Data::paginate(10,['name','description','type']);
        return $storedItems->count()==0?response()->json(["Message"=>"No Data Found"],404):
                                        response($storedItems,200);

    }

    /**
     * @OA\Get(
     *      path="/api/show/{id}",
     *      summary="view single stored data",
     *      @OA\Response(
     *           name, description,type,image_url
     *           response=200,
     *           )
     *       ),     
     */   
    public function show($id)
    {
      
        if (Data::where('id', $id)->exists()) {
            $data = Data::find($id);
            $tempraryUrl=Storage::disk('local')->temporaryUrl($data->file, now()->addMinutes(10));
            return response()->json(
                [
                  "name"=>$data->name,
                  "description"=>$data->description,
                  "tempraryUrl"=>$tempraryUrl,
                   "type"=>$data->type
                ]
                , 200);
          } else {
            return response()->json([
              "message" => "not found"
            ], 404);
          }       

    }
}
