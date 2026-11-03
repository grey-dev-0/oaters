<?php

namespace Tests\Feature\Ruby;

use Modules\Common\App\Models\Contact;
use Modules\Sapphire\App\Models\User;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class AttendanceExportTest extends AttendanceTestCase
{
    private function export(string $format, array $payload = [])
    {
        return $this->post('r/attendance/export', $this->dataTablesPayload($payload) + ['format' => $format], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ]);
    }

    private function departmentFilter(int $departmentId): array
    {
        $payload = $this->dataTablesPayload();
        $payload['columns'][2]['search']['value'] = [$departmentId];

        return $payload;
    }

    public function test_csv_export_matches_the_filtered_table_exactly(): void
    {
        $department = $this->makeDepartment('Filter Dept');
        $member = Contact::factory()->create(['name' => 'Filter Member']);
        $member->departments()->attach($department->id);
        $shift = $this->makeShift();

        $newer = $this->makePunch('in', 12, $shift, [
            'contact_id' => $member->id,
            'created_at' => '2026-11-01 09:12:00',
        ]);
        $older = $this->makePunch('out', -25, $shift, [
            'contact_id' => $member->id,
            'created_at' => '2026-10-31 17:25:00',
        ]);
        $outsider = $this->makePunch('in', 20, $shift, [
            'contact_id' => Contact::factory()->create(['name' => 'Outsider'])->id,
        ]);

        $payload = $this->departmentFilter($department->id);

        $tableRows = $this->attendanceRows($payload);
        $this->assertCount(2, $tableRows);
        $this->assertNotContains($outsider->id, array_column($tableRows, 'id'));

        $content = $this->export('csv', $payload)->assertOk()->streamedContent();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $content, 'CSV must start with a UTF-8 BOM');

        $lines = explode("\n", trim($content));

        $this->assertSame(
            ['Name', 'Role', 'Departments', 'Type', 'Punctuality', 'Created At'],
            str_getcsv(substr(array_shift($lines), 3)),
            'header row must match the table columns in order, without Actions'
        );
        $this->assertCount(2, $lines, 'export must contain exactly the filtered rows');
        $this->assertStringContainsString('Filter Member', $lines[0]);
        $this->assertStringContainsString('12 minutes late', $lines[0]);
        $this->assertStringContainsString('In', $lines[0]);
        $this->assertStringContainsString('Filter Dept', $lines[0]);
        $this->assertStringContainsString('25 minutes early', $lines[1]);
        $this->assertStringNotContainsString('Outsider', implode("\n", $lines));

        // Row order mirrors the table's default descending created-at sort
        // (cells use the table's d/m/Y h:i:s A timestamp format).
        $this->assertStringContainsString('01/11/2026', $lines[0]);
        $this->assertStringContainsString('31/10/2026', $lines[1]);
    }

    public function test_unfiltered_csv_export_includes_all_rows_with_empty_cells_for_shiftless_punches(): void
    {
        $shift = $this->makeShift();
        $withShift = $this->makePunch('in', 12, $shift, ['created_at' => '2026-11-01 09:12:00']);
        $withoutShift = $this->makePunch('in', null, null, ['created_at' => '2026-11-01 10:00:00']);

        $content = $this->export('csv')->assertOk()->streamedContent();
        $lines = explode("\n", trim($content));
        array_shift($lines);

        $this->assertCount(2, $lines);
        $shiftlessLine = collect($lines)->first(fn ($line) => ! str_contains($line, 'minutes'));
        $this->assertNotNull($shiftlessLine, 'shift-less punch must export with an empty punctuality cell');
        $this->assertStringNotContainsString('Unassigned', $shiftlessLine);
        $this->assertStringContainsString('12 minutes late', collect($lines)->first(fn ($line) => str_contains($line, 'minutes')));
    }

    public function test_csv_export_with_zero_matches_returns_headers_only(): void
    {
        $this->makePunch('in', 12, $this->makeShift());

        $content = $this->export('csv', $this->departmentFilter(999999))->assertOk()->streamedContent();

        $lines = explode("\n", trim($content));
        $this->assertCount(1, $lines);
        $this->assertStringContainsString('Punctuality', $lines[0]);
    }

    public function test_xlsx_export_contains_the_filtered_rows(): void
    {
        $department = $this->makeDepartment('Xlsx Dept');
        $member = Contact::factory()->create(['name' => 'Xlsx Member']);
        $member->departments()->attach($department->id);
        $this->makePunch('in', 20, $this->makeShift(), [
            'contact_id' => $member->id,
            'created_at' => '2026-11-01 09:20:00',
        ]);

        $content = $this->export('xlsx', $this->departmentFilter($department->id))->assertOk()->streamedContent();

        $this->assertStringStartsWith("PK\x03\x04", $content, 'response must be a valid XLSX (zip) container');

        $path = tempnam(sys_get_temp_dir(), 'oaters-export-test').'.xlsx';
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

        $this->assertSame(['Name', 'Role', 'Departments', 'Type', 'Punctuality', 'Created At'], $values[0]);
        $this->assertCount(2, $values);
        $this->assertContains('Xlsx Member', $values[1]);
        $this->assertContains('20 minutes late', $values[1]);
        $this->assertContains('Xlsx Dept', $values[1]);
    }

    public function test_export_denies_users_without_module_access(): void
    {
        $this->actingAs(User::create(['username' => 'stranger', 'password' => 'secret-password', 'active' => true]));

        $this->export('csv')->assertForbidden();
    }
}
