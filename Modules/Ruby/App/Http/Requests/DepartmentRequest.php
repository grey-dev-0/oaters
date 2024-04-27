<?php

namespace Modules\Ruby\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Ruby\App\Models\Department;
use Modules\Ruby\App\Models\Subordinate;

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
            $rules += ['id' => ['required', 'exists:r_departments,id']];
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
        if($this->id){
            $department = Department::find($this->id);
            $department->update($this->except(['manager_id', 'contact_id']));
        } else
            Department::create($this->except(['manager_id', 'contact_id']));
        if(!$this->id){
            if($this->manager_id || !empty($this->contact_id))
                $department->employees()->attach($this->contact_id ?: [null], $this->only('manager_id'));
        } elseif($department->head->first()?->id != $this->manager_id)
            $this->restructureHead($department, $this->manager_id, $this->contact_id);
        return response()->json(['success' => true, 'message' => trans('ruby::departments.'.($this->id?
            'updated' : 'created'))]);
    }

    /**
     * Restructures the management head of a department while maintaining the rest of its hierarchy.
     *
     * @param Department $department The department in question.
     * @param int $managerId The ID of the new head of department.
     * @param int[] $subordinateIds The IDs of direct subordinates of the new head.
     * @return void
     */
    private function restructureHead($department, $managerId, $subordinateIds){
        $currentHead = $department->head->first();
        $currentSubordinates = $currentHead?->subordinates()->wherePivot('department_id', $department->id)->get()?? collect();
        if($currentSubordinates->isNotEmpty()){
            $currentSubManagers = Subordinate::whereDepartmentId($this->id)
                ->whereIn('manager_id', $currentSubordinates->pluck('id'))->pluck('manager_id')->toArray();
            $currentNonManagers = $currentSubordinates->whereNotIn('id', $currentSubManagers);
            $maintainedSubordinates = $currentSubordinates->pluck('id')->intersect($subordinateIds)->values()->toArray();
            $maintainedSubordinates = array_values(array_unique(array_merge($currentSubManagers, $maintainedSubordinates)));
            $newSubordinates = array_values(array_diff($subordinateIds, $maintainedSubordinates));
            $removedSubordinates = $currentNonManagers->pluck('id')->diff($subordinateIds)->values()->toArray();
            Subordinate::whereDepartmentId($this->id)->whereManagerId($currentHead?->id)
                ->whereIn('contact_id', $maintainedSubordinates)->update(['manager_id' => $managerId]);
            if(!empty($newSubordinates))
                $department->employees()->attach($newSubordinates, ['manager_id' => $managerId]);
            if(!empty($removedSubordinates))
                $department->employees()->detach($removedSubordinates);
        } else{
            $department->employees()->detach();
            if($managerId || !empty($subordinateIds))
                $department->employees()->attach($subordinateIds ?: [null], ['manager_id' => $managerId]);
        }
    }
}
