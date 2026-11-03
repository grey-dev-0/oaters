<?php

namespace Tests\Feature\Ruby;

use Modules\Common\App\Models\Contact;
use Modules\Ruby\App\Models\Department;
use Modules\Ruby\App\Models\DepartmentLocale;
use Modules\Ruby\App\Models\Punch;
use Modules\Ruby\App\Models\Shift;
use Modules\Sapphire\App\Models\Role;
use Modules\Sapphire\App\Models\User;

class AttendanceTestCase extends StaffTestCase
{
    /**
     * Creates a shift with the given daily window.
     */
    protected function makeShift(string $start = '09:00', string $end = '17:00'): Shift
    {
        return Shift::create(['start' => $start, 'end' => $end]);
    }

    /**
     * Creates a punch, optionally with an assigned shift and recorded latency.
     */
    protected function makePunch(string $type, ?int $latency, ?Shift $shift = null, array $overrides = []): Punch
    {
        return Punch::create($overrides + [
            'contact_id' => Contact::factory()->create()->id,
            'type' => $type,
            'shift_id' => $shift?->id,
            'latency' => $latency,
        ]);
    }

    /**
     * Creates a localized department.
     */
    protected function makeDepartment(string $name): Department
    {
        $department = Department::create();
        DepartmentLocale::create(['department_id' => $department->id, 'locale' => 'en', 'name' => $name]);

        return $department;
    }

    /**
     * Grants the threshold-editing permission to the acting HR manager.
     */
    protected function grantSettingsPermission(): void
    {
        $this->hrRole()->givePermissionTo(
            \Spatie\Permission\Models\Permission::findOrCreate('manage-attendance-settings')
        );
    }

    /**
     * Signs in a user who can access Ruby but cannot edit attendance settings.
     */
    protected function actingAsTeamLead(): User
    {
        $role = Role::findOrCreate('team-lead');
        $role->givePermissionTo(\Spatie\Permission\Models\Permission::findOrCreate('ruby'));

        $user = User::create(['username' => 'teamlead', 'password' => 'secret-password', 'active' => true]);
        $user->assignRole($role);
        $this->actingAs($user);

        return $user;
    }

    /**
     * Posts a DataTables request to the attendance feed and returns the decoded rows.
     */
    protected function attendanceRows(array $payloadOverrides = []): array
    {
        $response = $this->postAjax('r/attendance', $this->dataTablesPayload($payloadOverrides));

        $response->assertOk();

        return $response->json('data');
    }

    /**
     * Posts a DataTables request and maps punch ids to their punctuality cells.
     */
    protected function attendancePunctualityById(array $payloadOverrides = []): array
    {
        return collect($this->attendanceRows($payloadOverrides))
            ->mapWithKeys(fn ($row) => [$row['id'] => $row['punctuality'] ?? ''])
            ->all();
    }

    /**
     * Builds a minimal DataTables server-side payload for the attendance table.
     */
    protected function dataTablesPayload(array $overrides = []): array
    {
        $column = fn (string $name, bool $orderable = true, bool $searchable = true) => [
            'name' => $name,
            'data' => $name,
            'orderable' => $orderable,
            'searchable' => $searchable,
            'search' => ['value' => ''],
        ];

        return $overrides + [
            'draw' => 1,
            'start' => 0,
            'length' => 100,
            'columns' => [
                $column('contact.name'),
                $column('contact.roles.title', true, false),
                $column('contact.departments.name', false, false),
                $column('type'),
                $column('r_punches.latency', true, false) + ['data' => 'punctuality'],
                $column('r_punches.created_at', true, false),
                $column('actions', false, false),
            ],
            'order' => [['column' => 5, 'dir' => 'desc']],
        ];
    }
}
