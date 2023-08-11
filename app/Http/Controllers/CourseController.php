<?php

namespace App\Http\Controllers;

use App\Http\Requests\Courses\StoreCourseRequest;
use App\Http\Requests\Courses\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected $model;

    public function __construct(Course $course)
    {
        $this->model = $course;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = $this->model->orderBy('id')->cursorPaginate(15);
        return response()->json([
            'data' => $courses
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        try
        {
            $data = $request->validated();
            $course = $this->model->create([

                'user_id' => auth()->user()->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'price' => $data['price']

            ]);

            if($course)
            {
                return response()->json([
                    'message' => 'Your course has been created, head over to lessons page to create your first lesson.'
                ], 200);
            }

            return response()->json([
                'message' => 'There is an issue creating your course.'
            ], 422);
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
            $course = $this->model->where('id', $id)
            ->with(['user:id,name'])
            ->get(['id', 'user_id', 'title', 'description', 'price']);

            return $course;
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request,string $id)
    {
        try
        {
            $this->authorize('update', $this->model);
            $data = $request->validated();
            $course = $this->model->findOrFail($id)->update($data);

            if($course)
            {
                return response()->json([
                    'message' => 'Your course has been updated, head over to lessons page to create your first lesson.'
                ], 200);
            }

            return response()->json([
                'message' => 'There is an issue updating your course.'
            ], 422);

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
            $this->authorize('delete', $this->model);
            $course = $this->model->whereId($id)->first();
            
            if($course)
            {
                $course->delete();

                return response()->json([
                    'message' => 'Your course has been deleted successfully.'
                ], 200); 
            }

            return response()->json([
                'message' => 'There was an error deleting your course.'
            ], 404); 
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }
}
