<?php

namespace FaceDigital\Crudify\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class BaseRepository
 *
 */
trait BaseRepository
{
    /**
     * @var Model Class
     */
    protected Model $entity;

    protected $fileEntity;

    /**
     * @var $identify
     */
    protected mixed $identify;

    /**
     * @return Model
     */
    public function getEntityClass()
    {
        return $this->entity;
    }

    /**
     * @param Model $model
     */
    public function setEntityClass(Model $model)
    {
        $this->entity = $model;
    }

    /**
     * @return string
     */
    public function getIdentify()
    {
        return $this->identify;
    }

    /**
     * @param string $identify
     */
    public function setIdentify(string $identify)
    {
        $this->identify = $identify;
        return $this;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function createNewEntity(array $data)
    {
        return $this->entity->create($data);
    }

    /**
     * @return mixed
     */
    public function search()
    {
        return $this->entity->useFilters()->dynamicPaginate();
    }


    /**
     * @return mixed
     */
    public function search_without_paginate()
    {
        return $this->entity->useFilters()->get();
    }
    public function search_one(){
        return $this->entity->useFilters()->firstOrFail();
    }
    /**
     * @return mixed
     */
    public function findAll(){
        return $this->entity->all();
    }

    public function countAll(){
        return $this->entity->count();
    }

    public function countByIdentify($identify){
        return $this->entity->where($this->identify, $identify)->count();
    }

    public function findInIdentify(array $values)
    {
        return $this->entity->whereIn($this->entity, $values)->get();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function paginateByIdentify($identify)
    {
        return $this->entity->where($this->identify, $identify)->dynamicPaginate();
    }

    public function searchByIdentify($identify, $classFilter)
    {
        return $this->entity->where($this->identify, $identify)->useFilters($classFilter)->dynamicPaginate();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findOneById(int $id)
    {
        return $this->entity->where('id', $id)->first();
    }

    /**
     * @param string $uuid
     * @return mixed
     */
    public function findOneByUuid(string $uuid)
    {
        return $this->entity->where('uuid', $uuid)->firstOrFail();
    }

    /**
     * @param mixed $identify
     * @return mixed
     */
    public function findOneByIdentify($identify)
    {
        return $this->entity->where($this->identify, $identify)->first();
    }

    public function findByIdentify($identify)
    {
        return $this->entity->where($this->identify, $identify)->get();
    }


    /**
     * @param mixed $identify
     * @param array $data
     * @return null
     */
    public function updateByIdentify($identify, array $data)
    {
        $entity = $this->entity->where($this->identify, $identify)->firstOrFail();

        if($entity)
            return tap($entity)->updateOrFail($data);
        return null;
    }

    /**
     * @param int $id
     * @param array $data
     * @return null
     */
    public function updateById(int $id, array $data)
    {
        $entity = $this->entity->where('id', $id)->firstOrFail();
        if ($entity){

            return tap($entity)->updateOrFail($data);
        }
        return null;
    }

    /**
     * @param string $uuid
     * @param array $data
     * @return null
     */
    public function updateByUuid(string $uuid, array $data)
    {
        $entity = $this->entity->where('uuid', $uuid)->firstOrFail();
        if($entity)
            return tap($entity)->updateOrFail($data);
        return null;
    }

    /**
     * @return mixed
     */
    public function deleteAll()
    {
        return $this->findAll()->deleteOrFail();
    }

    /**
     * @param mixed $identify
     * @return mixed
     */
    public function deleteByIdentify($identify)
    {
        return $this->entity->where($this->identify, $identify)->firstOrFail()->deleteOrFail();
    }

    /**
     * @param mixed $identify
     * @return mixed
     */
    public function deleteAllByIdentify($identify)
    {
        return $this->entity->where($this->identify, $identify)->delete();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function deleteById(int $id)
    {
        return $this->entity->where('id', $id)->firstOrFail()->deleteOrFail();
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function bulkDelete(array $ids)
    {
        return $this->entity->whereIn('id', $ids)->delete();
    }

    /**
     * @param string $uuid
     * @return mixed
     */
    public function deleteByUuid(string $uuid)
    {
        return $this->entity->where('uuid', $uuid)->firstOrFail()->deleteOrFail();
    }

    public function getLast()
    {
        return $this->entity->latest();
    }


}
