<?php

declare(strict_types=1);

use Castor\Attribute\AsListener;
use Castor\Event\AfterApplicationInitializationEvent;
use Castor\TaskDescriptorCollection;

#[AsListener(event: AfterApplicationInitializationEvent::class)]
function register_classes_methods_as_tasks(AfterApplicationInitializationEvent $event): void
{
    $classes = get_declared_classes();

    $classesMethodsDescriptor = [];
    foreach ($classes as $class) {
        $reflectedClass = new ReflectionClass($class);
        $taskClassAttributeList = $reflectedClass->getAttributes(AsTaskClass::class);
        if ($taskClassAttributeList === []) {
            continue;
        }

        $taskClassAttributeInstance = $taskClassAttributeList[0]->newInstance();
        foreach ($reflectedClass->getMethods(ReflectionMethod::IS_PUBLIC) as $methodReflection) {
            $reflectionAttributeList = $methodReflection->getAttributes(AsTaskMethod::class);
            if ($reflectionAttributeList === []) {
                continue;
            }

            /** @var AsTaskMethod $taskMethodInstance */
            $taskMethodInstance = $reflectionAttributeList[0]->newInstance();

            if ($taskMethodInstance->name === '') {
                $taskMethodInstance->name = strtolower(
                    preg_replace('/(?<!^)[A-Z]/', '-$0', $methodReflection->getName())
                );
            }

            if ($taskMethodInstance->namespace === null) {
                if ($taskClassAttributeInstance->namespace !== null) {
                    $taskMethodInstance->namespace = $taskClassAttributeInstance->namespace;
                } else {
                    $taskMethodInstance->namespace = $reflectedClass->getShortName();
                }
                // Slugify the namespace
                $taskMethodInstance->namespace = strtolower(
                    preg_replace('/(?<!^)[A-Z]/', '-$0', $taskMethodInstance->namespace)
                );
            }

            $functionReflection = new ReflectionFunction($methodReflection->getClosure($reflectedClass->newInstance()));
            $classesMethodsDescriptor[] = new Castor\Descriptor\TaskDescriptor($taskMethodInstance, $functionReflection);
        }
    }

    $event->taskDescriptorCollection = new TaskDescriptorCollection(
        [...$event->taskDescriptorCollection->taskDescriptors, ...$classesMethodsDescriptor],
        $event->taskDescriptorCollection->symfonyTaskDescriptors
    );
}
