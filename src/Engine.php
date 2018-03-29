<?php


namespace Valous\Annotation;

use Valous\Annotation\Parser\Data\Set;
use Valous\Annotation\Parser\IRule;
use Valous\Annotation\Parser\Parser;
use Valous\Annotation\Parser\Rule\MapClass;
use Valous\Core\Pattern\Singleton;


/**
 * @author David Valenta <david.valenta96@gmail.com>
 */
class Engine extends Singleton
{
    /** @var Parser */
    private $parser;


    /**
     * Engine constructor.
     */
    protected function __construct()
    {
        $this->parser = new Parser();
        $this->parser->addRule(new MapClass());
    }


    /**
     * @param $object
     * @return Set
     * @throws \ReflectionException
     */
    public function parse($object)
    {
        return $this->parser->parse($object);
    }


    /**
     * @param IRule $rule
     * @param $callback
     */
    public function registerParseRule(IRule $rule, $callback)
    {
        $this->parser->addRule($rule);
    }
}
