<?php

require '../vendor/autoload.php';

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

class ObserverDemo {

	protected $dispatcher;

	public $result = '1';

	public function __construct() {
		$this->dispatcher = new EventDispatcher();
		$this->dispatcher->addListener('execute.after', array('DemoPlugin', 'callback'));
		$this->dispatcher->addListener('execute.after', array('DemoPlugin', 'callback2'));
	}

	public function testOut() {
		echo "Output: " . $this->result . "\n";
	}

	public function execute() {
		echo "\n" . get_class($this) . ":\n\n";
		echo "Initial value: " . $this->result . "\n";
	}

}

class DemoPlugin {

	public static function callback(Event $e) {
		$e->getSubject()->result = '2';
		$e->getSubject()->testOut();
		if ($e->hasArgument('interrupt') && $e->getArgument('interrupt')) {
			$e->stopPropagation();
		}

	}

	public static function callback2(Event $e) {
		$e->getSubject()->result = '3';
		$e->getSubject()->testOut();
	}

}

// Demo1 executes all listeners
class Demo1 extends ObserverDemo {

	public function execute() {
		parent::execute();
		$this->dispatcher->dispatch('execute.after', new GenericEvent($this));
	}

}

// Demo2 executes the first listener which then ends propagation
class Demo2 extends ObserverDemo {

	public function execute() {
		parent::execute();
		$this->dispatcher->dispatch('execute.after', new GenericEvent($this, array('interrupt' => true)));
	}
}

$demo1 = new Demo1();
$demo1->execute();

$demo2 = new Demo2();
//$demo2->execute();
