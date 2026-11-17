<?php

namespace Tests\Feature\Ruby;

use Modules\Common\App\Models\Contact;
use Modules\Ruby\App\Models\Department;
use Modules\Ruby\App\Models\DepartmentLocale;
use Modules\Ruby\App\Models\Subordinate;

class StructurePageTest extends StaffTestCase
{
    private Contact $manager;

    private Contact $member;

    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postAjax('r/contacts/create', $this->memberPayload(['name' => 'Manager One', 'username' => 'managerone']));
        $this->postAjax('r/contacts/create', $this->memberPayload(['name' => 'Member One', 'username' => 'memberone']));
        $this->manager = Contact::where('name', 'Manager One')->firstOrFail();
        $this->member = Contact::where('name', 'Member One')->firstOrFail();
        $this->signInWithContact('Structure Tester');

        $this->department = Department::create();
        DepartmentLocale::create(['department_id' => $this->department->id, 'locale' => 'en', 'name' => 'Accounting']);
    }

    /**
     * Gives the signed-in user a profile contact, required by the module
     * layout's navbar when rendering full pages.
     */
    private function signInWithContact(string $name): void
    {
        $contact = Contact::create(['name' => $name]);
        auth()->user()->contact()->save($contact);
    }

    public function test_structure_page_renders_the_hierarchy(): void
    {
        Subordinate::create(['department_id' => $this->department->id, 'manager_id' => $this->manager->id,
            'contact_id' => $this->member->id]);

        $response = $this->get('r/contacts/structure');

        $response->assertOk();
        $response->assertSee('Manager One');
        $response->assertSee('Member One');
        $response->assertSee('Accounting');
    }

    public function test_structure_page_skips_manager_only_rows(): void
    {
        Subordinate::create(['department_id' => $this->department->id, 'manager_id' => $this->manager->id,
            'contact_id' => null]);

        $response = $this->get('r/contacts/structure');

        $response->assertOk();
        $this->assertSame('[]', $this->extractSubordinatesJson($response->getContent()),
            'a manager-only row carries no chart node');
    }

    public function test_structure_page_skips_deactivated_members(): void
    {
        Subordinate::create(['department_id' => $this->department->id, 'manager_id' => $this->manager->id,
            'contact_id' => $this->member->id]);
        // Deactivation normally clears subordinate rows; simulate the legacy
        // leftover row to prove the chart never references deactivated staff.
        $this->member->delete();

        $response = $this->get('r/contacts/structure');

        $response->assertOk();
        $this->assertStringNotContainsString('Member One', $this->extractSubordinatesJson($response->getContent()));
    }

    /**
     * Extracts the subordinates JSON embedded by structure.blade.php.
     */
    private function extractSubordinatesJson(string $html): string
    {
        preg_match('/let subordinates = (\[[\s\S]*?\]);/U', $html, $matches);

        return $matches[1] ?? '';
    }
}
