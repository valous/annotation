<?php


namespace Valous\Annotation\Parser;

use Valous\Annotation\Parser\Data\Set;


/**
 * @author David Valenta <david.valenta96@gmail.com>
 */
class Parser
{
    /** @var array */
    private $rules = [];


    /**
     * @param IRule $rule
     */
    public function addRule(IRule $rule)
    {
        $this->rules[get_class($rule)] = $rule;
    }


    /**
     * @param $object
     * @return Set
     * @throws \ReflectionException
     */
    public function parse($object)
    {
        $className  = get_class($object);
        $reflection = new \ReflectionClass($className);

        $data = new Set();

        $data->reflection   = $reflection;
        $data->properties   = $this->parseProperties($reflection->getProperties());
        $data->methods      = $this->parseMethods($reflection->getMethods());

        /** @var IRule $rule */
        foreach ($this->rules as $rule)
        {
            $rule->execute($data);
        }

        return $data;
    }


    /**
     * @param $methods
     * @return array
     */
    private function parseMethods($methods)
    {
        $data = [];

        /** @var \ReflectionMethod $method */
        foreach ($methods as $method)
        {
            $data[$method->getName()] = $this->parseDocComment($method->getDocComment());
        }

        return $data;
    }


    /**
     * @param $properties
     * @return array
     */
    private function parseProperties($properties)
    {
        $data = [];

        /** @var \ReflectionProperty $property */
        foreach ($properties as $property)
        {
            $data[$property->getName()] = $this->parseDocComment($property->getDocComment());
        }

        return $data;
    }


    /**
     * @param $docDocument
     * @return array
     */
    private function parseDocComment($docDocument)
    {
        $docDocument = str_replace(['/**', '*/'], '', $docDocument);

        $parts = explode('*', $docDocument);
        $data  = [];

        foreach ($parts as $part)
        {
            $keyValue   = explode(' ', trim($part), 2);

            $key        = str_replace('@', '', $keyValue[0], $count);
            if ($count == 0)
            {
                continue;
            }

            $value      = isset($keyValue[1]) ? $keyValue[1] : null;

            $data[$key][] = $value;
        }

        return $data;
    }
}
