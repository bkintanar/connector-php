<?php
namespace Dhtmlx\Connector\DataStorage;
use Dhtmlx\Connector\DataProcessor\DataProcessor;
use \Exception;

class PHPLaravelZenIntraDBDataWrapper extends ArrayDBDataWrapper {

	public function select($source) {
        $sourceData = $source->get_source();
        if(is_array($sourceData))	//result of find
            $res = $sourceData;
        else {
            $res = $sourceData->get()->toArray();
        }

		return new ArrayQueryWrapper($res);
	}

	protected function getErrorMessage() {
		$errors = $this->connection->getErrors();
		$text = array();
		foreach($errors as $key => $value)
			$text[] = $key." - ".$value[0];

		return implode("\n", $text);
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

    private function fill_model($obj, $data) {
        $dataArray = $data->get_data();
        unset($dataArray[DataProcessor::$action_param]);
        unset($dataArray[$this->config->id["db_name"]]);

        foreach($dataArray as $key => $value)
            $obj->$key = $value;

        return $obj;
    }

	protected function errors_to_string($errors) {
		$text = array();
		foreach($errors as $value)
			$text[] = implode("\n", $value);

		return implode("\n",$text);
	}

	public function escape($str) {
		throw new Exception("Not implemented");
	}

	public function query($str) {
		throw new Exception("Not implemented");
	}

	public function get_new_id() {
		throw new Exception("Not implemented");
	}

}
