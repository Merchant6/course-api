<?php

namespace App\Http\Requests\Lessons;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['required'],
            'title' => ['required', 'string', 'min:5', 'max:50'],
            'description'=> ['required', 'min:20', 'max:200'],
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/avi', 'max:10240']
        ];
    }
}
