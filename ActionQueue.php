<?php

// namespace ...;

use InvalidArgumentException;

class ActionQueue{
	private $queue = [];


	public function init(){
		return $this;
	}

	public function add(BaseAction $action)
	{
		$newAction = $action->get();

		if (empty($this->queue)) {
			$this->queue = $newAction;
		} else {
			$this->insertDeepest($this->queue, $newAction);
		}

		return $this;
	}

	private function insertDeepest(array &$queue, array $newAction)
	{
		if (!isset($queue["action"])) {
			// Eğer action alanı yoksa, yeni eklenen action buraya yerleştirilir
			$queue["action"] = $newAction;
		} else {
			// Eğer action varsa, en içe ulaşana kadar ilerler
			$this->insertDeepest($queue["action"], $newAction);
		}
	}


	public function queue(){
		return $this->queue;
	}
	public function get(){
		return [
			"action" => json_encode($this->queue)
		];
	}
}

abstract class BaseAction{
	

	abstract protected function type(): string;

	public function get()
	{
		$vars = get_object_vars($this);

		return [
			"type" => $this->type(),
			"data" => $vars
		];
	}
}

class NavigationAction extends BaseAction {


	public function __construct(public $name, public $screen, public $params) {
	}

	protected function type() : string
	{
		return "navigation";
	}

}

class ToastAction extends BaseAction
{
	public function __construct(public $type, public $title, public $textBody) {}

	protected function type(): string
	{
		return "toast";
	}
}

class DialogAction extends BaseAction
{
	/**
	 * @var DialogActionOption[] $options
	 */
	public array $options;

	public function __construct(public string $title, public string $textBody, array $options)
	{
		// Tip kontrolü: options içindeki tüm elemanlar DialogActionOption olmalı
		if(count($options) == 0){
			throw new InvalidArgumentException("Options değeri boş olamaz!");
		}
		foreach ($options as $option) {
			if (!$option instanceof DialogActionOption) {
				throw new InvalidArgumentException("Options sadece DialogActionOption nesneleri içerebilir.");
			}
		}

		// $this->options = array_map(fn($option) => [
		// 	"text" => $option->text,
		// 	...$option->actionQueue->x()
		// ], $options);

		$this->options = $options;

		// $this->options = array_map(fn(ActionQueue $option) =>  $option->get(), $options);
	}


	protected function type(): string
	{
		return "dialog";
	}

}

class DialogActionOption{

	public $action;
	public function __construct(public string $text, private ActionQueue $actionQueue){

		$this->action = $actionQueue->queue();
	}

}

class LinkAction  extends BaseAction
{
	public function __construct(public $url) {}

	protected function type(): string
	{
		return "link";
	}
}

class NotificationAction  extends BaseAction
{
	public function __construct(public $title, public $body) {}

	protected function type(): string
	{
		return "notification";
	}
}








