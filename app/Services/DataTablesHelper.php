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
        $filter = function($query, $column, $value){
            if(count($value) == 1)
                $query->whereDate($column, (new Carbon($value[0]))->toDateString());
            else{
                $query->whereBetween($column, [
                    (new Carbon($value[0]))->toDateString(),
                    (new Carbon($value[1]))->addDay()->toDateString()
                ]);
            }
        };
        foreach($attributes as $key)
            if(!empty($value = request("columns.{$columns[$key]}.search.value"))){
                $value = explode(' to ', $value);
                if(count($segments = explode('.', $key)) <= 1)
                    $filter($query, $key, $value);
                else{
                    $key = array_pop($segments);
                    $query->whereHas(implode('.', $segments), fn($related) => $filter($related, $key, $value));
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
            $datatable->editColumn($column, function($model) use($column, $format, $timezone){
                if(count($segments = explode('.', $column)) <= 1){
                    if(empty($model->$column))
                        return null;
                    else{
                        $datetime = new Carbon($model->$column);
                        if(!is_null($timezone))
                            $datetime->setTimezone($timezone);
                        return $datetime->format($format);
                    }
                } else{
                    $value = $model;
                    foreach($segments as $segment)
                        $value = $value?->$segment;
                    if(!empty($value)){
                        $value = new Carbon($value);
                        if(!is_null($timezone))
                            $value->setTimezone($timezone);
                        return $value->format($format);
                    }
                    return null;
                }
            });
    }
}