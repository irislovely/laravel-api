<?php

namespace App\Http\Controllers;

use App\Models\Files;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request('type')) {
            $files = Files::with('provider')->where('type','=',request('type'))->latest()->paginate(4);
        } elseif(request('date')) {
            $files = Files::with('provider')->whereDate('created_at', '=', request('date'))->latest()->paginate(4); //date('Y-m-d') - 2021-02-26
        } else {
            $files = Files::with('provider')->latest()->paginate(4);
        }
        
        return $files;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
        $validator = $this->validateUploadFields($request,$request->input('provider_id'));

        if($validator->fails()) {       
            return response()->json([
                "success" => false,
                "message" => "Error submitting file",
                'errors'=>$validator->errors()
            ],422);                   
        }

        $file = $request->file('file');
        $mimetype = $file->getMimetype();

        $fileName = time().'-'.$file->getClientOriginalName();
        $filePath = $file->storePubliclyAs('uploads', $fileName, 'public');
        $fileExt = $file->extension();

        $videoThumb = '';
        if ($mimetype == 'video/mp4' || $mimetype == 'video/quicktime') {
            $videoThumb = 'uploads/'.$this->createVideoThumb($file,$fileName);
        }        

        $dataFile = [
            'name'   => $request->input('name'),
            'filepath'     => '/storage/'.$filePath,
            'thumb'    => '/storage/'.$videoThumb,
            'type' => $fileExt,
            'provider_id' => $request->input('provider_id')
        ];

        $finalData = Files::create($dataFile);

        return response()->json([
            "success" => true,
            "message" => "File successfully uploaded",
            "file" => $finalData
        ]);
    }

    /**
     * Validate the inputs.
     */
    protected function validateUploadFields($request,$provider)
    {        
        $validated = '';
        $filerule = 'required';

        if($file = $request->file('file')) {
            $mimetype = $file->getMimetype();

            if($provider == 1){
                $filerule .= '|mimetypes:image/jpeg,video/mp4,audio/mpeg';

                switch ($mimetype) {
                    case 'image/jpeg':
                        $filerule .= '|dimensions:ratio=4/3|max:2048';
                        break;
                    case 'audio/mpeg':
                        $filerule .= '|max:5120';
                        break;                    
                    default:
                        break;
                }                

                $validated = Validator::make($request->all(),[
                    'name' => 'required|min:10|max:255',
                    'provider_id' => 'required',
                    'file' => explode("|",$filerule),                    
                ]);

                if ($mimetype == 'video/mp4' || $mimetype == 'audio/mpeg') {
                    $validated->after(function ($validated){
                        $duration = $this->checkDuration(request(),1);
                        if(!$duration){
                            $validated->errors()->add(
                                'file','Media duration is too long'
                            );
                        }
                    });
                }
            }

            if($provider == 2){
                $filerule .= '|mimetypes:image/jpeg,image/gif,video/mp4,video/quicktime';

                switch ($mimetype) {
                    case 'image/jpeg':
                        $filerule .= '|dimensions:ratio=16/9|max:5120';
                        break;
                    case 'image/gif':
                        $filerule .= '|dimensions:ratio=16/9|max:5120';
                        break;
                    case 'video/mp4':
                        $filerule .= '|max:51200';
                        break;
                    case 'video/quicktime':
                        $filerule .= '|max:51200';
                        break;                    
                    default:
                        break;
                }

                $validated = Validator::make($request->all(),[
                    'name' => 'required|min:10|max:255',
                    'provider_id' => 'required',
                    'file' => explode("|",$filerule),                    
                ]);

                if ($mimetype == 'video/mp4' || $mimetype == 'video/quicktime') {
                    $validated->after(function ($validated){
                        $duration = $this->checkDuration(request(),2);
                        if(!$duration){
                            $validated->errors()->add(
                                'file','Media duration is too long'
                            );
                        }
                    });   
                }
            }
        } else {
            $validated = Validator::make($request->all(),[
                'name' => 'required|min:10|max:255',
                'provider_id' => 'required',
                'file' => $filerule
            ]);
        }

        return $validated;
    }

    /**
     * Check the duration of video/audio file, based on provider.
     */
    protected function checkDuration($request,$provider){
        $file = $request->file('file');
        $mimetype = $file->getMimetype();

        $ffmpeg = FFMpeg::create();
        $duration = floor($ffmpeg->getFFProbe()->format($file)->get('duration'));

        if ($mimetype == 'video/mp4' || $mimetype == 'video/quicktime') {
            if($provider == 1)
                return $duration <= 60;
            if($provider ==2)
                return $duration <= 300;
        } elseif ($mimetype == 'audio/mpeg') {
            return $duration <= 30;
        }

    }

    /**
     * Create video thumbnails.
     */
    protected function createVideoThumb($file,$fileName) {
        $imageFile = pathinfo($fileName,PATHINFO_FILENAME).'-thumb.jpg';

        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($file);
        $duration = floor($ffmpeg->getFFProbe()->format($file)->get('duration'));

        $frame = $video->frame(TimeCode::fromSeconds($duration/2));
        $frame->save(storage_path('app/public').'/uploads/'.$imageFile);

        return $imageFile;
    }
}
