<?php


namespace Valous\Annotation\Parser;

use Valous\Annotation\Parser\Data\Set;


/**
 * @author David Valenta <david.valenta96@gmail.com>
 */
interface IRule
{
    public function execute(Set $set);
}
