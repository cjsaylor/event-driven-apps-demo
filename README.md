# Event Driven Applications Demo

This is an example application illustrating the usage of events via
Symfony's EventDispatcher library.

It illustrates both a observer setup as well as a "pubsub" setup.

This is refered to by the ["Event Driven Applications" presentation](http://slideshare.net/cjsaylor/event-driven-application).

### Install

```shell
php composer.phar install
```

### Running

Observer event demo:

```shell
php src/ObserverDemo.php
```

Subscriber event demo:

```shell
php src/SubscriberDemo.php
```
