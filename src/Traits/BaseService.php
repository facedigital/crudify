<?php

namespace FaceDigital\Crudify\Traits;

use Illuminate\Http\UploadedFile;

trait BaseService
{
    use UploadFileTrait;

    /**
     * @var
     */
    protected $baseService;

    /**
     * @var
     */
    public $ordernacao_default = '';

    /**
     * @var
     */
    protected $filtersName;

    protected $ordemDesc = [
        'nome_az' => 'nome A-Z',
        'nome_za' => 'nome Z-A',
        'id_az'   => 'mais novos primeiro',
        'id_za'   => 'mais antigos primeiro',
    ];

    /**
     * @return mixed
     */
    public function getBaseServiceClass()
    {
        return $this->baseService;
    }

    /**
     * @param mixed $model
     */
    public function setBaseServiceClass($class)
    {
        $this->baseService = $class;
    }


    /**
     * @return string
     */
    public function getOrdenacaoDefault()
    {
        return $this->ordernacao_default;
    }

    /**
     * @param string
     */
    public function setOrdenacaoDefault($ordernacao_default)
    {
        $this->ordernacao_default = $ordernacao_default;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function setIdentify(string $identify)
    {
        $this->baseService->setIdentify($identify);
        return $this;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function storeNew(array $data)
    {
        return $this->baseService->createNewEntity($data);
    }

    /**
     * @param string $identify
     * @return mixed
     */
    public function findOneByIdentify(string $identify)
    {
        return $this->baseService->findOneByIdentify($identify);
    }

    public function findByIdentify(string $identify){
        return $this->baseService->findByIdentify($identify);
    }

    /**
     * @param string $identify
     * @param array $data
     * @return mixed
     */
    public function changeByIdentify(string $identify, array $data)
    {
        return $this->baseService->updateByIdentify($identify, $data);
    }

    /**
     * @return mixed
     */
    public function search()
    {
        return $this->baseService->search();
    }

    public function search_without_paginate()
    {
        return $this->baseService->search_without_paginate();
    }

    public function search_one()
    {
        return $this->baseService->search_one();
    }

    /**
     * @param string $identify
     */
    public function deleteByIdentify(string $identify)
    {
        return $this->baseService->deleteByIdentify($identify);
    }

    /**
     * @param string $identify
     */
    public function deleteAllByIdentify(string $identify)
    {
        return $this->baseService->deleteAllByIdentify($identify);
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        return $this->baseService->findAll();
    }

    /**
     * @param string $uuid
     * @return mixed
     */
    public function findOneByUuid(string $uuid)
    {
        return $this->baseService->findOneByUuid($uuid);
    }

    /**
     * @param string $uuid
     * @param array $data
     * @return mixed
     */
    public function changeByUuid(string $uuid, array $data)
    {
        return $this->baseService->updateByUuid($uuid, $data);
    }

    /**
     * @param string $uuid
     */
    public function deleteByUuid(string $uuid)
    {
        return $this->baseService->deleteByUuid($uuid);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findOneById(int $id)
    {
        return $this->baseService->findOneById($id);
    }

    public function countAll()
    {
        return $this->baseService->countAll();
    }

    public function paginateByIdentify (string $identify){
        return $this->baseService->paginateByIdentify($identify);
    }

    public function searchByIdentify (string $identify, $classFilter) {
        return $this->baseService->searchByIdentify($identify, $classFilter);
    }
    public function countByIdentify(string $identify)
    {
        return $this->baseService->countByIdentify($identify);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function changeById(int $id, array $data)
    {
        return $this->baseService->updateById($id, $data);
    }

    /**
     * @param int $id
     */
    public function deleteById(int $id)
    {
        return $this->baseService->deleteById($id);
    }

    /**
     * @param array $ids
     */
    public function bulkDelete(array $ids)
    {
        return $this->baseService->bulkDelete($ids);
    }

     /**
      * Commons Functions
      * @return \Illuminate\Http\Request|array|mixed|string|null
      */

    private function getRequest(){
        return request();
    }

    public function getFiltersName()
    {
        return $this->filtersName;
    }

    public function setFiltersName(string $name)
    {
        $this->filtersName = $name;

        return $this;
    }

    private function getFiltersNameSession()
    {
        return session()->get('filtersName');
    }

    private function getFiltersDataName()
    {
        return $this->getFiltersName() . '.filtersData';
    }

    private function setFiltersNameSession($name)
    {
        return session()->put('filtersName', $name);
    }

    private function getFiltersDataSession()
    {
        return session()->get($this->getFiltersDataName());
    }

    private function setFiltersDataSession($data)
    {
        return session()->put($this->getFiltersDataName(), $data);
    }

    public function eraseFiltersDataSession()
    {
        return session()->forget($this->getFiltersDataName());
    }

    public function getRequestFilters()
    {
        $request = $this->getRequest();

        if($request->getMethod() == 'POST')
        {
            $this->setFiltersDataSession($request->all());

            return $request->all();
        }

        return $this->getFiltersDataSession();
    }

    public function setFiltersValidade()
    {
        $filtersSessionName = $this->getFiltersNameSession();
        if (isNaN($filtersSessionName) || $filtersSessionName != $this->getFiltersName())
        {
            $this->setFiltersNameSession($this->getFiltersName());
        }

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST')
        {
            if(isNaN($request->get('ordem')) && !isNaN($this->getOrdenacaoDefault())){
                $request->merge(['ordem' => $this->getOrdenacaoDefault()]);
            }

            $this->setFiltersDataSession($request->all());

        } elseif( is_array($request->all()) && count($request->all()) < 1 && !isNaN($this->getOrdenacaoDefault())){
            $request->merge(['ordem' => $this->getOrdenacaoDefault()]);

            $this->setFiltersDataSession($request->all());
        }
    }

}
