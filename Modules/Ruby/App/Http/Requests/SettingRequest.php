<?php

namespace Modules\Ruby\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Modules\Ruby\App\Support\Punctuality;

class SettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'threshold' => ['required', 'integer', 'min:0', 'max:240'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function messages(): array
    {
        return [
            'threshold.required' => trans('ruby::attendance.validation_required'),
            'threshold.integer' => trans('ruby::attendance.validation_integer'),
            'threshold.min' => trans('ruby::attendance.validation_min'),
            'threshold.max' => trans('ruby::attendance.validation_max'),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // gated by the route's can:manage-attendance-settings middleware
    }

    /**
     * Handles the attendance settings update request.
     */
    public function handle(): JsonResponse
    {
        \Settings::set(Punctuality::KEY, $this->integer('threshold'));

        return response()->json([
            'success' => true,
            'message' => trans('ruby::attendance.saved'),
            'threshold' => $this->integer('threshold'),
        ]);
    }
}
