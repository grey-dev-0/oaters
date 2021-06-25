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
        foreach($attributes as $attribute)
            if(!empty($attribute = request("columns.{$columns[$attribute]}.search.value"))){
                $attribute = explode(' to  ', $attribute);
                if(count($attribute) == 1)
                    $query->whereDate('presenter_views.created_at', (new Carbon($attribute[0]))->toDateString());
                else{
                    $query->whereBetween('presenter_views.created_at', [
                        (new Carbon($attribute[0]))->toDateString(),
                        (new Carbon($attribute[1]))->addDay()->toDateString()
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