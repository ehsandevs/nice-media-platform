<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVideoRequest;
use App\Jobs\ConvertVideoForDownloading;
use App\Jobs\ConvertVideoForStreaming;
use App\Models\Video;

class VideoController extends Controller
{
    public function store(StoreVideoRequest $request)
    {
        $video = Video::create([
            'disk'          => 'original_videos',
            'original_name' => $request->video->getClientOriginalName(),
            'path'          => $request->video->store('','original_videos'),
            'title'         => $request->title,
        ]);

        $this->dispatch(new ConvertVideoForDownloading($video));
        $this->dispatch(new ConvertVideoForStreaming($video));

        return response()->json([
            'id' => $video->id,
        ], 201);
    }
}
