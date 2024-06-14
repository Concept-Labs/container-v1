<?php
namespace XTC\Container\Preference;


trait PreferenceReflectionTrait
{

    /**
     * @var \ReflectionClass|null The reflection class
     */
    protected ?\ReflectionClass $reflectionClass = null;

    /**
     * Check if the service is instantiable
     *
     * @return boolean
     */
    protected function isInstantiable(): bool
    {
        return $this->getReflectionClass()->isInstantiable();
    }

     /**
      * Instantiate the preference using constructor
      *
      * @param mixed ...$parameters The constructor parameters
      *
      * @return object The service instance
      */
    protected function newInstance(...$parameters): object
    {
        $this->service = $this->getReflectionClass()
            ->newInstance(...$parameters);

        return $this->service;
    }
    
    /**
     * Instantiate the preference using constructor
     *
     * @param array $parameters The constructor parameters array
     * 
     * @return object @return object The service instance
     */
    protected function newInstanceArgs(array $parameters): object
    {
        $this->service = $this->getReflectionClass()
            ->newInstanceArgs($parameters);

        return $this->service;
    }

    /**
     * Check if a service can be instantiated without constructor
     *
     * @return boolean true if aservice can be instantiated without constructor
     */
    protected function canInstanceWithoutConstructor(): bool
    {
        return $this->getReflectionClass()->isInstantiable()
            && (
                null === $this->getConstructor()
                || (
                    $this->getConstructor()->isPublic() 
                    && $this->getConstructor()->getNumberOfRequiredParameters() === 0
                    && null === $this->getParameters()
                )
            );
    }

    /**
     * Instantiate the service without a constructor
     *
     * @return object The service instance
     */
    protected function newInstanceWithoutConstructor(): object
    {
        $this->service = $this->getReflectionClass()
            ->newInstanceWithoutConstructor();

        return $this->service;
    }

    
    /**
     * Get the service reflection
     *
     * @return \ReflectionClass|null The reflection class instance or null
     */
    protected function getReflectionClass()
    {
        if ($this->reflectionClass === null) {
            $a = class_exists($this->getClass());
            //try {
                $this->reflectionClass = new \ReflectionClass($this->getClass());
            // } catch (\Throwable $e) {
            //      throw new \RuntimeException($e->getMessage());
            // }
        }
        return $this->reflectionClass;
    }

    /**
     * Get the service constructor reflection
     *
     * @return \ReflectionMethod|null The service constructor reflection or null
     */
    public function getConstructor()
    {
        return $this->getReflectionClass()
            ->getConstructor();
    }

    /**
     * Get the service constructor reflection parameters
     *
     * @return \ReflectionParameter[] The reflection parameters
     */
    public function getConstructorParameters(): array
    {
        if (null !== $this->getConstructor()) {
            return $this->getConstructor()
                ->getParameters();
        }
        return [];
    }

    /**
     * @param string $name
     * 
     * @return mixed
     */
    public function getReflectionConstant(string $name)
    {
        return $this->getReflectionClass()
            ->getConstant($name);
    }

    


}