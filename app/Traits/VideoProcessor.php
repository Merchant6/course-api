<?php

namespace App\Traits;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

trait VideoProcessor
{
    protected $model;

    public function __construct(Lesson $model)
    {
        $this->model = $model;
    }

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

    public function getNamefromUrl(string $id)
    {
        $url = $this->model->where('id', $id)->first();
        $name = explode('/', parse_url($url->video_url)['path'])[3];
        return $name;
    }
}