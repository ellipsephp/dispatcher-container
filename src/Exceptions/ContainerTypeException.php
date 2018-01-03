<?php declare(strict_types=1);

namespace Ellipse\Dispatcher\Exceptions;

use UnexpectedValueException;

class ContainerTypeException extends UnexpectedValueException implements DispatcherExceptionInterface
{
    public function __construct($value)
    {
        $template = "A value of type %s was returned from the container factory - implementation of Psr\Container\ContainerInterface expected";

        $msg = sprintf($template, is_object($value) ? get_class($value) : gettype($value));

        parent::__construct($msg);
    }
}
