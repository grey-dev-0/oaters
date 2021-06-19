<?php namespace Modules\Common\Services;

class Chart{
    /**
     * @var \Illuminate\Support\Collection|array Input data that requires translation to ChartJS compatible data.
     */
    private $input;

    /**
     * @var bool $multiple Indicates whether input data comprises of multiple datasets or a single one.
     */
    private $multiple;
    
    /**
     * @var array $output The prepared chart data.
     */
    private $output;

    /**
     * Returns prepared linear data for ChartJS according to given data and range.
     *
     * @param $data \Illuminate\Support\Collection|array The data that requires processing.
     * @param $range ?array The range of the chart as "start" and "end" Carbon dates, integer range or, selection of points.
     * @param $multiple ?bool Indicates whether input data comprises of multiple datasets or a single one.
     * @return array The derived chart data.
     */
    public function getLinearChart($data, $range = null, $multiple = false)
    {
        $this->input = $data;
        $this->multiple = $multiple;
        $this->output = ['labels' => [], 'data' => []];
        switch(true){
            case ($range['start'?? null instanceof \Carbon\Carbon]):
                $this->processInDateRange($range['start'], $range['end']);
        }
        return $this->output;
    }

    /**
     * Returns processed chart data within the given range of start and end dates.
     * 
     * @param \Carbon\Carbon $startDate The range's beginning.
     * @param \Carbon\Carbon $endDate The range's end.
     */
    private function processInDateRange($startDate, $endDate){
        if (($range = $endDate->diffInDays($startDate)) >= 90 || $range >= 730) {
            for ($startDate = clone $startDate; $startDate->lte($endDate); $startDate->addDay()) {
                $label = $startDate->format('Y-m-d');
                if ($range >= 730) {
                    $groupIndex = $startDate->format('Y');
                    $this->output['labels'][$groupIndex] = $startDate->format('Y');
                } else {
                    $groupIndex = $startDate->format('Y-n');
                    $this->output['labels'][$groupIndex] = $startDate->format('M');
                }
                if(!$this->multiple)
                    $this->groupSingleDataset($label, $groupIndex);
                else
                    $this->groupMultipleDataset($label, $groupIndex);
            }
            $this->output['labels'] = array_values($this->output['labels']);
            if(!$this->multiple)
                $this->output['data'] = array_values($this->output['data']);
            else
                $this->clearDuplicates();
        } else {
            for ($startDate = clone $startDate; $startDate->lte($endDate); $startDate->addDay()) {
                $label = $startDate->format('Y-m-d');
                $this->output['labels'][] = $startDate->format('M j');
                if(!$this->multiple)
                    $this->output['data'][] = $this->input[$label]?? 0;
                else
                    foreach($this->input as $dataset)
                        $this->output['data'][$dataset][] = $this->input[$dataset][$label]?? 0;
            }
        }
    }

    /**
     * Cascades large series for single dataset.
     *
     * @param string $label The x point of the original data.
     * @param string $groupIndex The x point of the cascaded data.
     */
    private function groupSingleDataset($label, $groupIndex){
        $count = (isset($this->input[$label])) ? $this->input[$label] : 0;
        if (isset($this->output['data'][$groupIndex]))
            $this->output['data'][$groupIndex] += $count;
        else
            $this->output['data'][$groupIndex] = $count;
    }

    /**
     * Cascades large series for multiple datasets.
     *
     * @param $label
     * @param $groupIndex
     */
    private function groupMultipleDataset($label, $groupIndex){
        foreach($this->input as $dataset){
            $count = (isset($this->input[$dataset][$label])) ? $this->input[$dataset][$label] : 0;
            if (isset($this->output['data'][$dataset][$groupIndex]))
                $this->output['data'][$dataset][$groupIndex] += $count;
            else
                $this->output['data'][$dataset][$groupIndex] = $count;
        }
    }

    /**
     * Clears potential duplicate data in multiple datasets after grouping.
     */
    private function clearDuplicates(){
        foreach($this->output['data'] as $dataset)
            $this->output['data'][$dataset] = array_values($this->output['data'][$dataset]);
    }
}