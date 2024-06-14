<?php
namespace XTC\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;
use XTC\Container\Exception\ContainerException;

class NotFoundException extends ContainerException 
    implements NotFoundExceptionInterface
{}