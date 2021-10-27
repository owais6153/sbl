<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Redirect;
use Session;
use App\Models\FileUpload;
use App\Models\OnHand;
use App\Models\OnReciving;
use File;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ImportCSV;

class FileUploadController extends Controller
{
    public function importFiles()
    {
        return view('file_upload');
    }
    public function saveImportFiles(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'file_upload' => 'required',
            'type' => 'required',            
        ]);
 
        if ($validation->fails())
        {
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;               
            }
            return Redirect::back()->with('danger', $error_array);
        }
        $file_extension = $request->file_upload->getClientOriginalExtension();
        if ($file_extension != 'csv') {
            $error_array[][0] = 'Please Upload Csv File';
            return Redirect::back()->with('danger', $error_array);
        }
        $fileName = time().'-' .$this->gernateRandomNumber() .'.'.$file_extension; 
        
        $path = public_path('uploads');
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

        if($request->file_upload->move(public_path('uploads'), $fileName)){
            $FileUpload = new FileUpload();
            $FileUpload->user_id = Session::get('id');
            $FileUpload->filename = $fileName;
            $FileUpload->type = $request->type;
            $FileUpload->save();

            $file = public_path('uploads/' . $fileName);

            $dbtable = '';
            if($request->type == 'on_reciving')
                $dbtable = 'on_reciving';
            else
                $dbtable = 'on_hand';


            if (!file_exists( $file) || !is_readable( $file)){
                $error_array[][0] = 'File Not Found';
                return Redirect::back()->with('danger', $error_array);
            }
            else{
                $batch  = Bus::batch([])->dispatch();
                $batch->add(new ImportCSV($file, $dbtable, Session::get('id'), $FileUpload->id));
            }
        }
        else{
            $error_array[][0] = 'Upload Failed';
            return Redirect::back()->with('danger', $error_array);
        }


        return Redirect::back()->with('success', 'Uploaded Succesfully');
    }

    public function gernateRandomNumber() {
        $number = mt_rand(1000000000, 9999999999); 
        return $number;
    }

    public function batch()
    {
        $batchId = request('id');
        return Bus::findBatch($batchId);
    }

    public function batchInProgress()
    {
        $batches = DB::table('job_batches')->where('pending_jobs', '>', 0)->get();
        if (count($batches) > 0) {
            return Bus::findBatch($batches[0]->id);
        }

        return [];
    }
}
