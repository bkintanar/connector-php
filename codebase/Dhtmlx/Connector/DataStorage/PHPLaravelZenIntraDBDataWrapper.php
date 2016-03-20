<?php

namespace Dhtmlx\Connector\DataStorage;

class PHPLaravelZenIntraDBDataWrapper extends PHPLaravelDBDataWrapper {

	public function select($source) {
        $sourceData = $source->get_source();
        if(is_array($sourceData))	//result of find
            $res = $sourceData;
        else {
            $res = $sourceData->get()->toArray();
        }

		return new ArrayQueryWrapper($res);
	}

	public function insert($data, $source) {
		$element = $source->get_source()->first();
		$gantt_id = $element->gantt_id;
		$data->add_field('gantt_id', $gantt_id);

		$className = get_class($source->get_source()->first());
        $obj = $className::create();
        $this->fill_model($obj, $data)->save();

        $fieldPrimaryKey = $this->config->id["db_name"];
        $data->success($obj->$fieldPrimaryKey);
	}

	public function delete($data, $source) {
		$className = get_class($source->get_source()->first());
        $className::destroy($data->get_id());
        $data->success();
	}

	public function update($data, $source) {
        $className = get_class($source->get_source()->first());
        $obj = $className::find($data->get_id());
        $this->fill_model($obj, $data)->save();
        $data->success();
	}
}
