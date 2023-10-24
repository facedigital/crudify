<?php
namespace FaceDigital\Crudify\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session as FacadesSession;

class QueryFilters
{
    /**
     * @var Request
     */
    protected $request;

    protected $sessionFilterData;

    protected $sessionFilterSearch;

    /**
     * @var Builder
     */
    protected Builder $builder;

    protected array $allowedFilters             = [];
    protected array $allowedSorts               = [];
    protected array $allowedIncludes            = [];
    protected array $columnSearch               = [];
    protected array $columnSearchNotCollate     = [];
    protected array $relationSearch             = [];

    public function __construct()
    {
        $this->request = request();

        $this->sessionFilterData = !is_null(FacadesSession::get(FacadesSession::get('filtersName').'.filtersData'))
            ? FacadesSession::get(FacadesSession::get('filtersName').'.filtersData') : [];
        $this->sessionFilterSearch = isset($this->sessionFilterData['search']) ? $this->sessionFilterData['search'] : null;
    }

    private array $operations = [
        'applyFilters',
        'applyIncludes',
        'applySorts',
        'applySearch',
    ];

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        if (method_exists($this, 'before')) {
            $this->before();
        }

        foreach ($this->operations as $operation) {
            $this->$operation();
        }

        return $this->builder;
    }

    protected function filters(): array
    {
        $filters = $this->request->except('includes', 'sorts');
        if(isNaN($filters) || (count($this->request->all()) == 1 && $this->request->has('page'))){
            $filters = $this->sessionData();
            unset($filters['includes']);
            unset($filters['sorts']);
        }
        return $filters;
    }

    private function includes(): array
    {
        return explode(',', ($this->request->get('includes') ?: '' ));
    }

    private function sorts()
    {
        return $this->request->all('sorts')['sorts'];
    }

    private function search()
    {
        $search = $this->request->all('search')['search'];
        if(isNaN($search) || (count($this->request->all()) == 1 && $this->request->has('page'))){
            $search = $this->sessionSearch();
        }

        return $search;
    }

    private function sessionSearch()
    {
        return $this->sessionFilterSearch;
    }

    private function sessionData()
    {
        return $this->sessionFilterData;
    }


    private function applyFilters(): void
    {
        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name)) {
                $this->$name($value);
            }
            if (!empty($value) && in_array($name, $this->allowedFilters)) {
                if(is_array($value) && count($value) > 0){
                    $this->builder->whereIn($name, $value);

                    continue;
                }
                $value = explode(',', $value);

                if (count($value) > 1) {
                    $this->builder->whereIn($name, $value);

                    continue;
                }

                $this->builder->where($name, '=', $value);
            }
        }
    }

    private function applyIncludes(): void
    {
        $includes = array_intersect($this->includes(), $this->allowedIncludes);

        $this->builder->with($includes);
    }

    private function applySorts(): void
    {
        if ($this->sorts() != null) {
            $first_sort = explode(',', $this->sorts())[0];

            $value = ltrim($first_sort, '-');

            if (in_array($value, $this->allowedSorts)) {
                $this->builder->orderBy($value, $this->getDirection($first_sort));
            }
        }
    }

    private function getDirection(string $sort): string
    {
        return strpos($sort, '-') === 0 ? 'desc' : 'asc';
    }

    private function applySearch()
    {
        if (is_null($this->search())) {
            return;
        }
        $keyword = $this->search();
        $columns = $this->columnSearch;
        $columnsExceptCollate = $this->columnSearchNotCollate;

        $this->builder->where(function ($query) use ($keyword, $columns, $columnsExceptCollate) {
            if (count($columns) > 0) {
                foreach ($columns as $key => $column) {

                    $clause = $key == 0 ? 'whereRaw' : 'orWhereRaw';

                    $collate = '';
                    if (
                        DB::connection()->getDriverName() == 'oracle'
                        && (
                            count($columnsExceptCollate) < 1
                            || !in_array($column, $columnsExceptCollate)
                        )
                    ) {
                        $collate = 'COLLATE BINARY_CI';
                    }
                    $query->$clause($column . " ". $collate ."  LIKE '%" . $keyword . "%'");

                }
            }
            $this->searchByRelationship($query, $keyword);
        });
    }

    private function searchByRelationship($query, $keyword)
    {
        $relativeTables = $this->relationSearch;
        foreach ($relativeTables as $relationship => $relativeColumns) {
            $query->orWhereHas($relationship, function ($relationQuery) use ($keyword, $relativeColumns) {
                foreach ($relativeColumns as $key => $column) {
                    $clause = $key == 0 ? 'whereRaw' : 'orWhereRaw';

                    $collate = '';
                    if (DB::connection()->getDriverName() == 'oracle') {
                        $collate = 'COLLATE BINARY_CI';
                    }

                    $relationQuery->$clause($column ." " . $collate . "  LIKE '%" . $keyword . "%'");

                }
            });
        }
    }
    protected function joined($query, $table) {
        $joins = $query->getQuery()->joins;

        if($joins == null) {
            return false;
        }
        foreach ($joins as $join) {

            if ($join->table == $table) {
                return true;
            }
        }
        return false;
    }
}
