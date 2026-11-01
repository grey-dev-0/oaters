<?php

namespace Tests\Feature\Ruby;

use Modules\Common\App\Models\Contact;
use Modules\Ruby\App\Models\Department;
use Modules\Ruby\App\Models\Subordinate;
use Modules\Sapphire\App\Models\User;

class ContactRemovalTest extends StaffTestCase
{
    private Contact $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postAjax('r/contacts/create', $this->memberPayload());
        $this->member = Contact::firstOrFail();
    }

    public function test_member_with_history_is_deactivated_with_account_suspended(): void
    {
        $this->member->punches()->create(['type' => 'in']);

        $response = $this->postAjax('r/contacts/delete', ['id' => $this->member->id]);

        $response->assertOk();
        $member = Contact::withTrashed()->find($this->member->id);
        $this->assertNotNull($member->deleted_at, 'soft deleted');
        $this->assertFalse($member->user->active, 'account suspended');
        $this->assertSame(0, Contact::count(), 'hidden from the default staff list');
        $this->assertSame(1, Contact::withTrashed()->count(), 'history retained');
    }

    public function test_member_without_history_is_permanently_removed_with_account(): void
    {
        $response = $this->postAjax('r/contacts/delete', ['id' => $this->member->id]);

        $response->assertOk();
        $this->assertSame(0, Contact::withTrashed()->count(), 'no trace remains');
        $this->assertSame(0, User::where('username', 'janedoe')->count(), 'no orphaned login survives');
    }

    public function test_managed_departments_survive_managerless(): void
    {
        $department = Department::create();
        Subordinate::create(['department_id' => $department->id, 'manager_id' => $this->member->id, 'contact_id' => null]);
        $this->member->punches()->create(['type' => 'in']);

        $this->postAjax('r/contacts/delete', ['id' => $this->member->id])->assertOk();

        $this->assertNotNull($department->fresh(), 'department intact');
        $this->assertSame(0, Subordinate::where('department_id', $department->id)->count(), 'no dangling assignments');
    }

    public function test_deactivated_member_can_be_reactivated(): void
    {
        $this->member->punches()->create(['type' => 'in']);
        $this->postAjax('r/contacts/delete', ['id' => $this->member->id])->assertOk();

        $response = $this->postAjax('r/contacts/restore', ['id' => $this->member->id]);

        $response->assertOk();
        $member = Contact::find($this->member->id);
        $this->assertNotNull($member, 'back in the staff list');
        $this->assertTrue($member->user->active, 'account access restored');
        $this->assertSame(1, $member->punches()->count(), 'history intact');
    }

    public function test_inactive_filter_lists_only_deactivated_members(): void
    {
        $this->member->punches()->create(['type' => 'in']);
        $this->postAjax('r/contacts/create', $this->memberPayload(['name' => 'Second Member', 'username' => 'second']));
        $this->postAjax('r/contacts/delete', ['id' => $this->member->id])->assertOk();

        $response = $this->postAjax('r/contacts', ['status' => 'inactive', 'columns' => array_map(fn ($name) => ['name' => $name, 'search' => ['value' => '']], [
            'name', 'emails.address', 'phones.number', 'roles', 'job', 'departments', 'applicant.recruited_at',
        ])]);
        $response->assertOk();
        $this->assertSame(1, count($response->json('data')), 'only the deactivated member is listed');
        $this->assertNotNull($response->json('data.0.deleted_at'));
    }
}
