<?php

namespace Valous\Annotation\Parser\Rule;

use Valous\Annotation\Parser\Data\Set;
use Valous\Annotation\Parser\IRule;


/**
 * @author David Valenta <david.valenta96@gmail.com>
 */
class MapClass implements IRule
{
    /**
     * @param Set $set
     */
    public function execute(Set $set)
    {
        $uses = $this->getClassUses($set->reflection->getFileName());
        $this->map($set->methods, $uses);
        $this->map($set->properties, $uses);
    }


    /**
     * @param $array
     * @param $uses
     */
    private function map(&$array, $uses)
    {
        foreach ($array as $name => $values)
        {
            foreach ($values as $key => $value)
            {
                foreach ($value as $index => $attr)
                {
                    $attrParts = explode('$', $attr, 2);
                    if (isset($attrParts[1]))
                    {
                        $values[$key][$attrParts[1]] = isset($uses[$attrParts[0]]) ? $uses[$attrParts[0]] : $attrParts[0];
                        unset($values[$key][$index]);
                    }
                    else
                    {
                        $values[$key][isset($uses[$attr]) ? $uses[$attr] : $attr] = "";
                    }

                }
            }

            $array[$name] = $values;
        }
    }


    /**
     * @param string $filename
     * @return array
     */
    private function getClassUses($filename)
    {
        $content = file_get_contents($filename);

        preg_match_all("/use\s+(([a-zA-Z0-9\\\]+)(\s*as\s*[a-zA-Z0-9]+)*);/", $content, $uses);

        $response = [];
        foreach ($uses[1] as $use)
        {
            $useParts = explode(' as ', $use, 2);
            if (isset($useParts[1]))
            {
                $response[trim($useParts[1])] = trim($useParts[0]);
            }
            else
            {
                $tempParts = explode('\\', $useParts[0]);
                $response[trim(array_pop($tempParts))] = trim($useParts[0]);
            }
        }

        return $response;
    }
}
