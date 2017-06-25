<?php

namespace PlentyConnector\Connector\DefinitionFactory;

use PlentyConnector\Connector\ValidatorService\ValidatorServiceInterface;
use PlentyConnector\Connector\ValueObject\Definition\Definition;

/**
 * Class DefinitionFactory
 */
class DefinitionFactory
{
    /**
     * @var ValidatorServiceInterface
     */
    private $validator;

    /**
     * DefinitionFactory constructor.
     *
     * @param ValidatorServiceInterface $validator
     */
    public function __construct(ValidatorServiceInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param string   $originAdapterName
     * @param string   $destinationAdapterName
     * @param string   $objectType
     * @param null|int $priority
     * @param bool     $active
     *
     * @return Definition
     */
    public function factory(
        $originAdapterName,
        $destinationAdapterName,
        $objectType,
        $priority = null,
        $active = true
    ) {
        $definition = new Definition();
        $definition->setOriginAdapterName($originAdapterName);
        $definition->setDestinationAdapterName($destinationAdapterName);
        $definition->setObjectType($objectType);
        $definition->setPriority($priority);
        $definition->setActive($active);

        $this->validator->validate($definition);

        return $definition;
    }
}
