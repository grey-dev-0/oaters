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
    private function getColumns(){
        return array_flip(array_column(request('columns'), 'name'));
    }
}