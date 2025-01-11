<?php

namespace App\Grid;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property-read array $links
 * @property-read GridActionCollection $actions
 */
class GridRowCollection implements Arrayable
{

    protected GridActionCollection $actions;

    protected LengthAwarePaginator $paginator;

    private array $rows = [];

    public function __construct(
        private int $page,
        private Grid $_grid
    )
    {
        $this->actions = new GridActionCollection();
        $this->init();
    }

    private function init(){
        /** @var Model $model */
        $model = new ($this->_grid->getModelClass());

        $builder = $model->newQuery();


        // aplicamos los creterios de busqueda
        $request = request();
        if($request->has('query') && $request->has('column')){
            $col = str_replace('-','.',$request->query('column'));
            $builder->where($col,'like','%'.$request->query('query').'%');
        }
        $builder->orderBy('id','DESC');

        $cols = $this->_grid->getColumns()->getKeys();
        /** @var LengthAwarePaginator $paginator */
        $this->paginator = $builder->select($cols)->paginate(15);
        $this->paginator->withQueryString();

        foreach ($this->paginator->items() as $item) {
            $this->rows[] = new GridRow($item,$this);
        }

    }

    public function toArray(): array
    {
        return array_map(function (GridRow $row){
            return $row->toArray();
        },$this->rows);
    }


    public function __get(string $name): mixed
    {
        return match ($name) {
            'actions' => $this->actions,
            'links' => $this->paginator->linkCollection()->toArray(),
            default => null
        };
    }
}
