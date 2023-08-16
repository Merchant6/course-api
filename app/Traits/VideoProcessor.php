<?php

namespace App\Traits;
use Illuminate\Support\Facades\Storage;

trait VideoProcessor
{
    /**
     * Process the video from the request to be saved in the storage
     * @return boolean
     */
    public function process(object $video)
    {
        $fileName = $video->getClientOriginalName();

        $store = $video->storeAs('', $fileName, 'lessons');
        
        return $store;
    }
}