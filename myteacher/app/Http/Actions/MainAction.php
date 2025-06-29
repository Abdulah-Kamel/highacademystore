<?php

namespace App\Http\Actions;

class MainAction
{
    protected $model;
    protected $fileFolder;

    public function clearRequesrt($data)
    {
        foreach ($data as $key => $value) {
            $trimedValue = (gettype($value) == "string") ? trim($value) : $value;
            if (empty($trimedValue)) {
                unset($data[$key]);
                continue;
            }
            $data[$key] = $trimedValue;
        }
        return $data;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function first()
    {
        return $this->model->first();
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function store($data)
    {
        $data = $this->clearRequesrt($data);
        return $this->model->create($data);
    }

    public function update($id, $data)
    {
        $data = $this->clearRequesrt($data);
        return $this->model->where("id", $id)->update($data);
    }

    public function get()
    {
        return $this->model->get();
    }

    public function getWhere($where)
    {
        return $this->model->where($where)->get();
    }

    public function getWhereIn($column,$where)
    {
        return $this->model->whereIn($column,$where)->get();
    }

    public function lastId()
    {
        $row = $this->model->orderBy('id', 'DESC')->first();
        return (isset($row->id)) ? ($row->id + 1) : 1;
    }


    public function deleteWithFile($id, $column)
    {
        $obj = $this->find($id);
        deleteFile($obj->{$column});
        $this->delete($id);
    }

    public function updateWhere($where, $data)
    {
        return $this->model->where($where)->update($data);
    }

    public function findToArray($id)
    {
        return $this->model->find($id)->toArray();
    }

    public function deleteWhere($where)
    {
        return $this->model->where($where)->delete();
    }


    public function UpdateOrStore($where,$data){
        $data["created_by_id"] = auth('admin')->user()->id;
        return $this->model->updateOrCreate($where,$data);
    }

}
