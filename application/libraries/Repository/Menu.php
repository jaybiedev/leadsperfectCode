<?php

namespace Library\Repository;

class Menu extends \Library\Repository\RepositoryAbstract {


    public function getMenu($path)
    {

        /**
         * This could have been done by PG ltree but to make compatible with My
         */
        $sql = "
            WITH RECURSIVE node_rec AS (
            (SELECT 1 AS depth, ARRAY[id] AS menu_id, *
            FROM   menu
            WHERE  parent_id IS NULL
            )
            UNION ALL
            SELECT rec.depth + 1, rec.menu_id || menu.parent_id, menu.*
            FROM   node_rec rec
            JOIN   menu  ON menu.parent_id = rec.id
            WHERE  rec.depth < 4
            AND menu.path like '{$path}%'
            AND menu.enabled
            )
            SELECT *
            FROM   node_rec
            WHERE enabled AND path like '{$path}%'
            ORDER  BY parent_id  NULLS FIRST, sort_order;";

        
        $sql = "SELECT * FROM menu";
        return $this->db->query($sql);
    }
}