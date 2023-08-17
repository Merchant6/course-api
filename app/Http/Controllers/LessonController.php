<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lessons\StoreLessonRequest;
use App\Http\Requests\Lessons\UpdateLessonRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Traits\VideoProcessor;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    use VideoProcessor;
    protected $model;

    protected $publicPath;

    public function __construct(Lesson $model)
    {
        $this->model = $model;
        $this->publicPath = public_path() . '/storage/lessons/';
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

                if(\Storage::disk('lessons')->exists($video->getClientOriginalName()))
                {
                    return response()->json(['message' => 'File already exists']);
                }

                $processedFile = $this->process($video);

                $course = $this->model->create([

                    'course_id' => $data['course_id'],
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'video_url' => config('filesystems.disks.lessons.url'). DIRECTORY_SEPARATOR . $processedFile

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
        try
        {
            $lesson = $this->model->where('id', $id)
            ->with(['course:id,title,description,price'])
            ->get(['id', 'title', 'description', 'video_url', 'course_id']);
            
            return $lesson;
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLessonRequest $request, string $id)
    {
        try
        {
            $lesson = $this->model->findOrFail($id);
            $courseUserId = $lesson->course->user_id;

            $this->authorize('update', $courseUserId);
            $data = $request->validated();
            if($data)
            {
                $lesson->update($data);
                if($lesson)
                {
                    return response()->json([
                        'message' => 'Your lesson has been updated.'
                    ], 200);
                }
            }

            return response()->json([
                'message' => 'There was an error updating your lesson.'
            ], 422);

            // return $course;

        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try
        {
            $lesson = $this->model->findOrFail($id);
            $courseUserId = $lesson->course->user_id;

            $this->authorize('delete', $courseUserId);
            
            if($lesson)
            {
                $lesson->delete();

                return response()->json([
                    'message' => 'Your lesson has been deleted successfully.'
                ], 200); 
            }

            return response()->json([
                'message' => 'There was an error deleting your lesson.'
            ], 404); 
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }
}
