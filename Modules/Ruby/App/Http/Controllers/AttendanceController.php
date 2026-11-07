<?php

namespace Modules\Ruby\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Ruby\App\Http\Requests\SettingRequest;
use Modules\Ruby\App\Models\DepartmentLocale;
use Modules\Ruby\App\Models\Punch;
use Modules\Ruby\App\Support\Punctuality;
use Modules\Sapphire\App\Models\RoleLocale;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\CSV\Writer as CsvWriter;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

class AttendanceController extends Controller
{
    public function getIndex()
    {
        $roles = RoleLocale::whereLocale($locale = app()->getLocale())->whereHas('role', fn ($role) => $role->whereIn('name', ['general-manager', 'financial-manager', 'hr-manager', 'director', 'team-lead', 'accountant', 'hr-assistant', 'employee']))->pluck('title', 'role_id');
        $departments = DepartmentLocale::whereLocale($locale)->pluck('name', 'department_id');

        return view('ruby::attendance', compact('roles', 'departments'));
    }

    public function postIndex()
    {
        return tap(\DataTables::of($this->attendanceQuery()), fn ($dataTable) => \DataTablesHelper::formatTimestampColumns($dataTable, ['created_at']))
            ->addColumn('actions', fn () => view('ruby::actions.attendance'))
            ->addColumn('punctuality', fn (Punch $punch) => $this->renderPunctuality($punch))
            ->filter(function ($query) {
                $this->filterAttendance($query);
            })->rawColumns(['actions', 'punctuality'])->make();
    }

    public function getSettings()
    {
        return response()->json(['threshold' => Punctuality::threshold()]);
    }

    public function postSettings(SettingRequest $request)
    {
        return $request->handle();
    }

    /**
     * Streams the attendance table — filtered exactly like the on-screen table — as
     * an Excel (.xlsx) or CSV download.
     */
    public function postExport()
    {
        $format = request('format') == 'xlsx' ? 'xlsx' : 'csv';
        $headers = [
            trans('common::words.name'),
            trans('common::words.role'),
            trans('common::words.departments'),
            trans('common::words.type'),
            trans('ruby::attendance.punctuality'),
            trans('common::words.created_at'),
        ];
        $rows = $this->exportRows();

        return response()->streamDownload(
            fn () => $format == 'xlsx' ? $this->writeXlsx($headers, $rows) : $this->writeCsv($headers, $rows),
            'attendance_'.now()->format('Y-m-d').'.'.$format,
            ['Content-Type' => $format == 'xlsx'
                ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                : 'text/csv']
        );
    }

    /**
     * Builds the export rows by running the exact DataTables pipeline the table uses
     * (filters, search and ordering), with paging skipped so every matching punch is
     * included.
     */
    private function exportRows(): array
    {
        $dataTable = tap(\DataTables::of($this->attendanceQuery()), fn ($dataTable) => \DataTablesHelper::formatTimestampColumns($dataTable, ['created_at']))
            ->addColumn('punctuality', function (Punch $punch) {
                return ($reading = Punctuality::reading($punch)) ? $reading['text'] : '';
            })
            ->filter(function ($query) {
                $this->filterAttendance($query);
            })->skipPaging()->make();

        return array_map(function ($row) {
            $contact = $row['contact'] ?? [];

            return [
                $contact['name'] ?? '',
                $contact['roles'][0]['title'] ?? '',
                collect($contact['departments'] ?? [])->pluck('name')
                    ->merge(collect($contact['managed_departments'] ?? [])->pluck('name'))->implode(', '),
                trans('common::words.'.$row['type']),
                $row['punctuality'] ?? '',
                $row['created_at'] ?? '',
            ];
        }, $dataTable->getData(true)['data']);
    }

    /**
     * Writes the export as CSV (UTF-8 with BOM so Excel renders Arabic correctly).
     */
    private function writeCsv(array $headers, array $rows): void
    {
        $writer = new CsvWriter();
        $writer->openToFile('php://output');
        $this->writeWriterRows($writer, $headers, $rows);
    }

    /**
     * Writes the export as a plain Excel workbook.
     */
    private function writeXlsx(array $headers, array $rows): void
    {
        $writer = new XlsxWriter();
        $writer->openToFile('php://output');
        $this->writeWriterRows($writer, $headers, $rows);
    }

    /**
     * Feeds the given headers and rows to an openspout writer and closes it.
     */
    private function writeWriterRows(CsvWriter|XlsxWriter $writer, array $headers, array $rows): void
    {
        $writer->addRow(Row::fromValues($headers));
        $writer->addRows(array_map(fn ($row) => Row::fromValues($row), $rows));
        $writer->close();
    }

    /**
     * Renders a punch's punctuality cell (colored reading or neutral marker).
     */
    private function renderPunctuality(Punch $punch): string
    {
        return ($reading = Punctuality::reading($punch))
            ? '<span class="'.$reading['class'].'">'.$reading['text'].'</span>'
            : '<small class="text-muted">'.trans('common::words.unassigned').'</small>';
    }

    /**
     * The attendance feed base query shared by the table and the exporter.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function attendanceQuery()
    {
        return Punch::with([
            'contact' => fn ($q) => $q->withRoles()->withDepartments(),
        ])->select('r_punches.*');
    }

    /**
     * Applies the attendance table filters (date range, departments, roles) to the given query.
     */
    private function filterAttendance($query): void
    {
        \DataTablesHelper::filterByDate($query, ['r_punches.created_at'], false);
        $columns = \DataTablesHelper::getColumns();
        if (! empty($departments = request("columns.{$columns['contact.departments.name']}.search.value"))) {
            $query->whereHas('contact.departments', fn ($depts) => $depts->whereIn('r_departments.id', $departments));
        }
        if (! empty($roles = request("columns.{$columns['contact.roles.title']}.search.value"))) {
            $query->whereHas('contact.roles', fn ($r) => $r->whereIn('id', $roles));
        }
    }
}
