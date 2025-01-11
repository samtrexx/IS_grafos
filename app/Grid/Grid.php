<?php

namespace App\Grid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * @property-read GridToolbar $toolbar
 */
abstract class Grid extends \App\Http\Controllers\Controller {
    protected string $modelClass;
    protected string $title;
    protected string $resource;
    private GridToolbar $toolbar;

    protected string $page;

    protected string $view = 'Grid';
    protected GridColumnCollection $columns;
    protected GridRowCollection $rows;
    private string $_table;
    private Model $_model;
    protected bool $excludeStamps = true;

    /**
     * @throws \Exception
     */
    final protected function init(){
        $conn = DB::connection();
        $schemaBuilder = $conn->getSchemaBuilder();
        /** @var Model $model */
        $this->_model = new $this->modelClass;
        $this->_table = $this->_model->getTable();
        $columns = $schemaBuilder->getColumnListing($this->_table);
        if($this->excludeStamps)
            $columns = array_diff($columns,['created_at','updated_at']);

        $this->columns = new GridColumnCollection($columns,$this);

        $this->rows = new GridRowCollection(
            intval(request('page',1)),
            $this
        );

        $this->toolbar = new GridToolbar();

        $this->defaultActions();

        $this->mounted();

    }

    /**
     * @param Model $model
     * @return void
     */
    protected function mounted(){

    }

    protected function defaultActions(){
        $this->rows
            ->actions
            ->addAction(
                'Editar',
                route($this->resource . '.edit',[';id;']),
                'bi-pencil-square'
            )
            ->addAction(
                'Eliminar',
                route($this->resource . '.show',[';id;']),
                'bi-trash'
            );

        $this->toolbar
            ->actions
            ->addAction(
                'Crear',
                route($this->resource . '.create'),
                'bi-plus-square'
            );
    }

    final public function index()
    {
        $this->init();
        return Inertia::render($this->view,$this->toArray());
    }

    public function create(){
        return Inertia::render($this->page,[
            'url' => route($this->resource.'.store'),
            'method' => 'post',
            'backurl' => route($this->resource.'.index'),
        ]);
    }

    public function store(Request $request)
    {
        $rules = $this->defineRules();
        if(count($rules) > 0)
            $request->validate($rules);


        $item = $this->modelClass::create($request->all());

        if(!$item)
            throw new \Exception('No fue posible crear el registro');

        return response()->json([
            'url' => route($this->resource . '.index')
        ]);
    }

    public function edit($id,Request $request){
        $item = $this->modelClass::findOrFail($id);

        return Inertia::render($this->page,[
            'item' => $item,
            'url' => route($this->resource . '.update',[$id]),
            'method' => 'put'
        ]);
    }

    public function show($id,Request $request)
    {
        $item = $this->modelClass::findOrFail($id);

        return Inertia::render($this->page,[
            'item' => $item,
            'url' => route($this->resource . '.destroy',[$id]),
            'method' => 'delete',
            'destroy' => true
        ]);
    }

    public function update($id,Request $request)
    {
        $item = $this->modelClass::findOrFail($id);

        $attr = $request->except('_method');

        foreach($attr as $key => $value){
            $item->{$key} = $value;
        }

        $item->save();

        return response()->json([
            'url' => route($this->resource . '.index')
        ]);
    }

    public function destroy($id,Request $request){
        $this->modelClass::destroy($id);
        return response()->json([
            'url' => route($this->resource . '.index')
        ]);
    }


    protected function defineRules(): array {
        return [

        ];
    }


    public function getTableName(): string {
        return $this->_table;
    }

    public function getModel(): Model
    {
        return $this->_model;
    }

    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'columns' => $this->columns->toArray(),
            'rows' => $this->rows->toArray(),
            'toolbar' => $this->toolbar->toArray(),
            'pagination' => [
                'links' => $this->rows->links
            ]
        ];
    }

    public function getColumns(): GridColumnCollection
    {
        return $this->columns;
    }

    public function __get(string $name)
    {
        return match ($name) {
            'toolbar' => $this->toolbar,
            default => null,
        };
    }
}
