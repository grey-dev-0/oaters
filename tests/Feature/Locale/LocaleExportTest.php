<?php

namespace Tests\Feature\Locale;

use Modules\Common\App\Models\Contact;
use Modules\Ruby\App\Models\Department;
use Modules\Ruby\App\Models\DepartmentLocale;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use Tests\Feature\Ruby\AttendanceTestCase;

class LocaleExportTest extends AttendanceTestCase
{
    private function export(string $format, array $payload = [])
    {
        return $this->post('r/attendance/export', $this->dataTablesPayload($payload) + ['format' => $format], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ]);
    }

    /**
     * Creates a department carrying an Arabic name plus an English fallback,
     * attached to the given member.
     */
    private function bilingualDepartment(Contact $member, string $arName, string $enName): Department
    {
        $department = Department::create();
        DepartmentLocale::create(['department_id' => $department->id, 'locale' => 'ar', 'name' => $arName]);
        DepartmentLocale::create(['department_id' => $department->id, 'locale' => 'en', 'name' => $enName]);
        $member->departments()->attach($department->id);

        return $department;
    }

    public function test_arabic_csv_export_carries_arabic_headers_punctuality_and_data(): void
    {
        session(['locale' => 'ar']);

        $member = Contact::factory()->create(['name' => 'Arabic Member']);
        $this->bilingualDepartment($member, 'قسم الاختبار', 'Bilingual Dept');
        $this->makeDepartment('English Only Dept')->employees()->attach($member->id);

        $this->makePunch('in', 12, $this->makeShift(), [
            'contact_id' => $member->id,
            'created_at' => '2026-11-01 09:12:00',
        ]);

        $content = $this->export('csv')->assertOk()->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $content, 'the UTF-8 BOM must survive the Arabic export');

        $this->assertStringContainsString('الاسم', $content, 'Arabic header for Name');
        $this->assertStringContainsString('الالتزام بالمواعيد', $content, 'Arabic header for Punctuality');
        $this->assertStringContainsString('تاريخ الإنشاء', $content, 'Arabic header for Created At');
        $this->assertStringContainsString('قسم الاختبار', $content, 'localized department name in Arabic');
        $this->assertStringContainsString('English Only Dept', $content, 'missing Arabic value falls back to English (FR-003)');
        $this->assertStringContainsString('12 دقائق متأخر', $content, 'punctuality reading in Arabic');

        $this->assertStringNotContainsString('12 minutes late', $content);
        $this->assertStringNotContainsString('Punctuality', $content);
    }

    public function test_the_english_export_stays_unchanged(): void
    {
        session(['locale' => 'en']);

        $member = Contact::factory()->create(['name' => 'English Member']);
        $this->makePunch('in', 12, $this->makeShift(), [
            'contact_id' => $member->id,
            'created_at' => '2026-11-01 09:12:00',
        ]);

        $content = $this->export('csv')->assertOk()->streamedContent();

        $this->assertStringContainsString('12 minutes late', $content);
        $this->assertStringContainsString('Punctuality', $content);
        $this->assertStringNotContainsString('الالتزام بالمواعيد', $content);
    }

    public function test_arabic_xlsx_export_carries_arabic_headers_and_readings(): void
    {
        session(['locale' => 'ar']);

        $member = Contact::factory()->create(['name' => 'Arabic Member']);
        $this->bilingualDepartment($member, 'قسم الاختبار', 'Bilingual Dept');
        $this->makePunch('in', 20, $this->makeShift(), [
            'contact_id' => $member->id,
            'created_at' => '2026-11-01 09:20:00',
        ]);

        $content = $this->export('xlsx')->assertOk()->streamedContent();

        $this->assertStringStartsWith("PK\x03\x04", $content, 'response must be a valid XLSX (zip) container');

        $path = tempnam(sys_get_temp_dir(), 'oaters-locale-export').'.xlsx';
        file_put_contents($path, $content);

        $reader = new XlsxReader();
        $reader->open($path);

        $sheet = $reader->getSheetIterator()->current();
        $values = [];
        foreach ($sheet->getRowIterator() as $row) {
            $values[] = $row->toArray();
        }
        $reader->close();
        unlink($path);

        $this->assertSame(
            ['الاسم', 'الدور', 'الأقسام', 'النوع', 'الالتزام بالمواعيد', 'تاريخ الإنشاء'],
            $values[0],
            'the Arabic header row must match the export columns in order'
        );

        $dataRow = $values[1];
        $this->assertContains('قسم الاختبار', $dataRow);
        $this->assertTrue(
            collect($dataRow)->contains(fn ($cell) => str_contains((string) $cell, 'دقائق')),
            'punctuality reading must be Arabic'
        );
    }
}
