<?php

namespace App\Services;

use Carbon\Carbon;

class Chart{
    const SINGLE_POINT = 0;
    const MULTI_POINT = 1;

    /**
     * @var int $type The type of chart's data either single point e.g. pie or, multi point e.g. line.
     */
    private $type;

    /**
     * @var array $datasets Chart's datasets
     */
    private $datasets = [];

    /**
     * @var string[] $labels Chart's x-axis labels - of linear charts - or, pie chart labels.
     */
    private $labels = [];

    /**
     * @var bool $ranged Determines whether x-axis data is ranged
     * to cascade large datasets by consecutive x values and, fill in non-existing x values with zero y's.
     */
    private $ranged = false;

    /**
     * @var int[]|Carbon[] The start and the end of chart's data as integer values or datetimes on x-axis.
     */
    private $range = [];

    /**
     * Creates pie chart instance from provided data, labels and, required colors.
     *
     * @param array $data Array of numerical data.
     * @param string[] $labels Labels to be assigned to each datum.
     * @param string[] $colors Colors to be used with each datus.
     * @return $this
     */
    public function pie($data, $labels, $colors){
        $this->type = self::SINGLE_POINT;
        $this->datasets[] = compact('data') + ['backgroundColor' => $colors];
        $this->labels = $labels;
        return $this;
    }

    /**
     * Creates doughnut chart instance from provided data, labels and, required colors.
     *
     * @param array $data Array of numerical data.
     * @param string[] $labels Labels to be assigned to each datum.
     * @param string[] $colors Colors to be used with each datus.
     * @return $this
     */
    public function doughnut($data, $labels, $colors){
        return $this->pie($data, $labels, $colors);
    }

    /**
     * Sets pie / doughnut chart labels and appends numerical data into the labels for better chart view.
     */
    private function setPieLabels(){
        foreach($this->labels as $i => $label)
            $labels[$i] = "$label ({$this->datasets[0]['data'][$i]})";
        $this->labels = $labels;
    }

    /**
     * Creates a line chart instance from the provided dataset
     * or, adds it to an exising instance if called in a chart chain.
     *
     * @param array $data A linear dataset where keys represent x-axis and values represent y-axis.
     * @param string $label The label of the dataset.
     * @param string $borderColor The color of the dataset's line.
     * @param string $backgroundColor The color that will be rendered under the dataset's line.
     * @return $this
     */
    public function line($data, $label, $borderColor, $backgroundColor){
        $this->type = self::MULTI_POINT;
        $this->datasets[] = compact('data', 'label', 'borderColor', 'backgroundColor') + ['borderWidth' => 1, 'fill' => true];
        return $this;
    }

    /**
     * Creates a bar chart instance from the provided dataset
     * or, adds it to an exising instance if called in a chart chain.
     *
     * @param array $data A linear dataset where keys represent bar label and values represent bar height or value.
     * @param string $label The label of the dataset.
     * @param string $borderColor The color of the dataset's line.
     * @param string $backgroundColor The color that will be rendered under the dataset's line.
     * @return $this
     */
    public function bar($data, $label, $borderColor, $backgroundColor){
        return $this->line($data, $label, $borderColor, $backgroundColor);
    }

    /**
     * Moves line / bar chart labels from each of the provided datasets - uniquely - to the x-axis labels.
     */
    private function setLineLabels(){
        foreach($this->datasets as &$dataset)
            $this->labels = array_merge(array_keys($dataset['data']));
        $this->labels = array_values(array_unique($this->labels));
        if($this->ranged)
            $this->setRangedLineData();
        else
            foreach($this->datasets as &$dataset)
                $dataset['data'] = array_values($dataset['data']);
    }

    /**
     * Sets the range of the datasets x values in the chart.
     *
     * @param ?int|Carbon $start The range's start
     * @param ?int|Carbon $end The range's end
     * @return $this
     */
    public function range($start = null, $end = null){
        $this->ranged = true;
        if(is_null($start)){
            [$start, $end] = explode(' to ', request('range'));
            $start = new Carbon($start);
            $end = (new Carbon($end))->endOfDay();
        }
        $this->range = [$start, $end];
        return $this;
    }

    /**
     * Setting chart to (non)ranged chart.
     *
     * @param bool $ranged Determines whether the chart should be ranged.
     * @return $this
     */
    public function ranged($ranged = true){
        $this->ranged = $ranged;
        return $this;
    }

    /**
     * Processes collected data as ranged data i.e. setting zero values for non-existing x-axis values
     * and, grouping large datasets by cascading y-axis values for each set of consecutive x-values in range.
     */
    private function setRangedLineData(){
        $rangeStart = $this->range[0]?? $this->labels[0];
        if($rangeStart instanceof Carbon || preg_match('#^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?#', $rangeStart)){
            $this->setRangedDatetimeLabels();
            $this->setRangedDatetimeData();
        }
    }

    /**
     * Sets bar / line labels of ranged chart with datetime x-axis values.
     */
    private function setRangedDatetimeLabels(){
        $startDate = $this->range[0]->copy();
        $endDate = $this->range[1]->copy();
        $labels = [];
        if (($range = $endDate->diffInDays($startDate)) >= 90 || $range >= 730) {
            for ($startDate = clone $startDate; $startDate->lte($endDate); $startDate->addDay()) {
                if ($range >= 730) {
                    $groupIndex = $startDate->format('Y');
                    $labels[$groupIndex] = $startDate->format('Y');
                } else {
                    $groupIndex = $startDate->format('Y-n');
                    $labels[$groupIndex] = $startDate->format('M');
                }
            }
            $labels = array_values($labels);
        } else
            for ($startDate = clone $startDate; $startDate->lte($endDate); $startDate->addDay())
                $labels[] = $startDate->format('M j');
        $this->labels = $labels;
    }

    /**
     * Sets bar / line data i.e. y-axis values of ranged chart with datetime x-axis values.
     */
    private function setRangedDatetimeData(){
        foreach($this->datasets as &$dataset){
            $startDate = $this->range[0]->copy();
            $endDate = $this->range[1]->copy();
            $data = [];
            if (($range = $endDate->diffInDays($startDate)) >= 90 || $range >= 730) {
                for ($startDate = clone $startDate; $startDate->lte($endDate); $startDate->addDay()) {
                    $label = $startDate->format('Y-m-d');
                    $groupIndex = ($range >= 730)?  $startDate->format('Y') : $startDate->format('Y-n');
                    $value = $dataset['data'][$label]?? 0;
                    if (isset($data[$groupIndex]))
                        $data[$groupIndex] += $value;
                    else
                        $data[$groupIndex] = $value;
                }
            } else
                for ($startDate = clone $startDate; $startDate->lte($endDate); $startDate->addDay()){
                    $label = $startDate->format('Y-m-d');
                    $data[] = $dataset['data'][$label]?? 0;;
                }
            $dataset['data'] = $data;
        }
    }

    /**
     * Renders and gets ChartJS compatible data to be directly passed to the library.
     * Gets the data based on the created object and its set properties using other methods beforehand.
     *
     * @return array
     */
    public function get(){
        if($this->type == self::SINGLE_POINT)
            $this->setPieLabels();
        else
            $this->setLineLabels();
        return [
            'datasets' => $this->datasets,
            'labels' => $this->labels
        ];
    }
}