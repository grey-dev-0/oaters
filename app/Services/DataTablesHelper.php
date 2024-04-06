<?php namespace App\Services;

use Carbon\Carbon;

class DataTablesHelper{
    /**
     * Adds "whereDate" or "whereBetween" on the given datatables query according to submitted date and / or date ranges.
     *
     * @param \Illuminate\Database\Query\Builder $query The query to altered.
     * @param string[] $attributes The date attributes to filter by.
     */
    public function filterByDate(&$query, $attributes){
        $columns = $this->getColumns();
        foreach($attributes as $key)
            if(!empty($value = request("columns.{$columns[$key]}.search.value"))){
                $value = explode(' to ', $value);
                if(count($value) == 1)
                    $query->whereDate($key, (new Carbon($value[0]))->toDateString());
                else{
                    $query->whereBetween($key, [
                        (new Carbon($value[0]))->toDateString(),
                        (new Carbon($value[1]))->addDay()->toDateString()
                    ]);
                }
            }
    }

    /**
     * Gets the submitted datatables column indices keyed by their names.
     *
     * @return array
     */
    public function getColumns(){
        return array_flip(array_column(request('columns'), 'name'));
    }

    /**
     * Formats designated timestamp columns returned to datatables instance.
     *
     * @param mixed $datatable The datatable response.
     * @param string[] $columns The timestamp columns to format.
     * @param string $format The required timestamp format
     * @param ?string $timezone The timezone to set the columns to.
     */
    public function formatTimestampColumns(&$datatable, $columns, $format = 'd/m/Y h:i:s A', $timezone = null){
        foreach($columns as $column)
            $datatable->editColumn($column, function($tenant) use($column, $format, $timezone){
                if(empty($tenant->$column))
                    return null;
                else{
                    $datetime = new Carbon($tenant->$column);
                    if(!is_null($timezone))
                        $datetime->setTimezone($timezone);
                    return $datetime->format($format);
                }
            });
    }
}