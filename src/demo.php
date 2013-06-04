<?php

require '../vendor/autoload.php';

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

class Demo {

	protected $dispatcher;

	public $result = '1';

	public function __construct() {
		$this->dispatcher = new EventDispatcher();
		$this->dispatcher->addListener('execute1.after', array('DemoPlugin', 'callback'));
		$this->dispatcher->addListener('execute1.after', array('DemoPlugin', 'callback2'));
	}

	public function testOut() {
		echo "Output: " . $this->result . "\n";
	}

}

class Demo1 extends Demo {

	public function execute() {
		$this->testOut();
		$this->dispatcher->dispatch('execute1.after', new GenericEvent($this));
		echo "Final Output - ";
		$this->testOut();
	}

}

class Demo2 extends Demo {

	public function execute() {
		$this->testOut();
		$this->dispatcher->dispatch('execute1.after', new GenericEvent($this, array('interrupt' => true)));
		echo "Final Output - ";
		$this->testOut();
	}
}

class DemoPlugin {

	public static function callback(Event $e) {
		$e->getSubject()->result = '2';
		$e->getSubject()->testOut();
		$eventArgs = $e->getArguments();
		if ($e->hasArgument('interrupt') && $e->getArgument('interrupt')) {
			$e->stopPropagation();
		}

	}

	public static function callback2(Event $e) {
		$e->getSubject()->result = '3';
		$e->getSubject()->testOut();
	}

}

// Execution

echo "Demo 1:\n\n";
$demo1 = new Demo1();
$demo1->execute();

echo "\nDemo 2: \n\n";
$demo2 = new Demo2();
$demo2->execute();
