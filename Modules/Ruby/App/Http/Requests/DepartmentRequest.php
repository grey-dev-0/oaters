<?php

namespace Modules\Ruby\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Ruby\App\Models\Department;

class DepartmentRequest extends FormRequest{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array{
        $rules = [
            'en.name' => ['required', 'min:3', 'max:255'],
            'ar.name' => ['required', 'min:3', 'max:255'],
            'manager_id' => ['nullable', 'exists:lc_contacts,id'],
            'contact_id.*' => ['nullable', 'exists:lc_contacts,id']
        ];
        if($this->route()->getName() == 'ruby::departments.update')
            $rules += ['id' => ['required', 'exists:r_departments.id']];
        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool{
        return true;
    }

    /**
     * Handles department create / update requests.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(){
        $department = $this->id?
            Department::find($this->id)
                ->tap(fn($dept) => $dept->update($this->except(['manager_id', 'contact_id']))) :
            Department::create($this->except(['manager_id', 'contact_id']));
        if(!$this->id)
            $department->employees()->attach($this->contact_id?: [null], $this->only('manager_id'));
        return response()->json(['success' => true]);
    }
}
