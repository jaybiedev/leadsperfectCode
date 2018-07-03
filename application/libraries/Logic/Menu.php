<?php
namespace Library\Logic;

use LogicAbstract;

class Menu extends \Library\Logic\LogicAbstract {


    static public function getMenu($route) {

        $top = 'Top.' . ucwords($route);

        if (empty($top))
            return array();

        $Repository = new \Library\Repository\Menu();

        $Query = $Repository->getMenu($top);

        $menuArray = [];
        foreach ($Query->result('\Model\Menu') as $Menu) {

            $path = preg_replace("@^$top\.@", '', $Menu->path);
            $nodes = explode('.', $path);
            if (count($nodes) == 1 && !isset($menuArray[$path]))
            {
                $menuArray[$path] = $Menu;
            }
            else
            {
                $last_node = array_pop($nodes);
                $parent_path =  array_shift($nodes);
                $Parent = $menuArray[$parent_path];

                while ($Parent == null && !empty($nodes))
                {
                    $parent_path = implode('.', $nodes);
                    $Parent = $menuArray[$parent_path];
                    array_shift($nodes);
                }

                if (!is_object($Parent))
                {
                    // parent is not set stack this
                    $menuArray[$last_node] = $Menu;
                }
                else
                {
                    foreach ($nodes as $node)
                    {
                        $parent_path .= ".{$node}"; //parents: copy of object pointer
                        $Parent = $Parent->children[$parent_path];
                    }

                    $Parent->children[$path] = $Menu;
                }

            }

        }

        return $menuArray;

    }

}