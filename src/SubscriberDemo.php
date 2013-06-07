<?php

require '../vendor/autoload.php';

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubscriberDemo {

	protected $dispatcher;

	public $result = '1';

	public function __construct() {
		$this->dispatcher = new EventDispatcher();
		$this->dispatcher->addSubscriber(new DemoPlugin());
	}

	public function testOut() {
		echo "Output: " . $this->result . "\n";
	}

}

class DemoPlugin implements EventSubscriberInterface {

	public static function getSubscribedEvents() {
		return array(
			'execute.before' => 'executeBefore',
			'execute.after' => 'executeAfter'
		);
	}

	public function executeBefore(Event $e) {
		$e->getSubject()->result = '2';
		$e->getSubject()->testOut();
	}

	public function executeAfter(Event $e) {
		$e->getSubject()->result = '3';
		$e->getSubject()->testOut();
	}

}

class Demo1 extends SubscriberDemo {

	public function execute() {
		echo "\nDemo1:\n\n";
		$this->testOut();
		$this->dispatcher->dispatch('execute.before', new GenericEvent($this));

		// Some logic would exist in between

		$this->dispatcher->dispatch('execute.after', new GenericEvent($this));
	}

}

$demo1 = new Demo1();
$demo1->execute();
