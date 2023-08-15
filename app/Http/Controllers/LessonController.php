<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lessons\StoreLessonRequest;
use App\Http\Requests\Lessons\UpdateLessonRequest;
use App\Models\Lesson;
use App\Traits\VideoProcessor;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    use VideoProcessor;
    protected $model;

    public function __construct(Lesson $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lessons = $this->model->orderBy('id')->cursorPaginate(15);
        return $lessons;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLessonRequest $request)
    {
        try
        {
            $data = $request->validated();

            $video = $data['video'];
            if($video)
            { 
                $processedFile = $this->process($video);
                $course = $this->model->create([

                    'course_id' => $data['course_id'],
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'video_url' => config('filesystems.disks.lessons.url'). '/' . $processedFile

                ]);

                if($course)
                {
                    return response()->json([
                        'message' => 'Your lesson has been created, head over to lessons page to see your newly created lesson.'
                    ], 200);
                }

                return response()->json([
                    'message' => 'There is an issue creating your lesson.'
                ], 422);
            }

            
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLessonRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
