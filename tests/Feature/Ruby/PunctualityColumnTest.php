<?php

namespace Tests\Feature\Ruby;

use Modules\Ruby\App\Support\Punctuality;

class PunctualityColumnTest extends AttendanceTestCase
{
    public function test_renders_all_six_punctuality_rule_cells(): void
    {
        $shift = $this->makeShift();

        $blueLateIn = $this->makePunch('in', 12, $shift);
        $redLateIn = $this->makePunch('in', 20, $shift);
        $boldEarlyIn = $this->makePunch('in', -10, $shift);
        $blueEarlyOut = $this->makePunch('out', -8, $shift);
        $redEarlyOut = $this->makePunch('out', -25, $shift);
        $boldLateOut = $this->makePunch('out', 30, $shift);
        $greenOnTime = $this->makePunch('out', 0, $shift);

        $cells = $this->attendancePunctualityById();

        $this->assertStringContainsString('text-primary', $cells[$blueLateIn->id]);
        $this->assertStringContainsString('12 minutes late', $cells[$blueLateIn->id]);

        $this->assertStringContainsString('text-danger', $cells[$redLateIn->id]);
        $this->assertStringContainsString('20 minutes late', $cells[$redLateIn->id]);

        $this->assertSame(
            1,
            substr_count($cells[$boldEarlyIn->id], 'font-weight-bold'),
            'early ins must be bold green'
        );
        $this->assertStringContainsString('text-success', $cells[$boldEarlyIn->id]);
        $this->assertStringContainsString('10 minutes early', $cells[$boldEarlyIn->id]);

        $this->assertStringContainsString('text-primary', $cells[$blueEarlyOut->id]);
        $this->assertStringContainsString('8 minutes early', $cells[$blueEarlyOut->id]);

        $this->assertStringContainsString('text-danger', $cells[$redEarlyOut->id]);
        $this->assertStringContainsString('25 minutes early', $cells[$redEarlyOut->id]);

        $this->assertStringContainsString('text-success font-weight-bold', $cells[$boldLateOut->id]);
        $this->assertStringContainsString('30 minutes late', $cells[$boldLateOut->id]);

        $this->assertStringContainsString('text-success', $cells[$greenOnTime->id]);
        $this->assertStringNotContainsString('font-weight-bold', $cells[$greenOnTime->id]);
        $this->assertStringContainsString('on time', $cells[$greenOnTime->id]);
    }

    public function test_renders_neutral_marker_for_shiftless_punches(): void
    {
        $punch = $this->makePunch('in', null);

        $cells = $this->attendancePunctualityById();

        $this->assertStringContainsString('text-muted', $cells[$punch->id]);
        $this->assertStringContainsString('Unassigned', $cells[$punch->id]);
        $this->assertStringNotContainsString('minutes', $cells[$punch->id]);
    }

    public function test_threshold_change_flips_colors_on_next_request(): void
    {
        $shift = $this->makeShift();
        $punch = $this->makePunch('in', 6, $shift);

        $this->assertStringContainsString('text-primary', $this->attendancePunctualityById()[$punch->id]);

        \Settings::set(Punctuality::KEY, 5);

        $this->assertStringContainsString('text-danger', $this->attendancePunctualityById()[$punch->id]);
    }

    public function test_punctuality_column_sorts_by_recorded_latency(): void
    {
        $shift = $this->makeShift();
        $late = $this->makePunch('in', 20, $shift);
        $early = $this->makePunch('in', -10, $shift);
        $mid = $this->makePunch('in', 12, $shift);

        $rows = $this->attendanceRows(['order' => [['column' => 4, 'dir' => 'asc']]]);

        $this->assertSame(
            [$early->id, $mid->id, $late->id],
            array_column($rows, 'id')
        );
    }

    public function test_punctuality_values_localize_to_arabic(): void
    {
        app()->setLocale('ar');

        $shift = $this->makeShift();
        $late = $this->makePunch('in', 12, $shift);
        $onTime = $this->makePunch('out', 0, $shift);

        $cells = $this->attendancePunctualityById();

        $this->assertStringContainsString('12 دقائق متأخر', $cells[$late->id]);
        $this->assertStringContainsString('في الوقت المحدد', $cells[$onTime->id]);
    }
}
