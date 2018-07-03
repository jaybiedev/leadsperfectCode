<?php

namespace Library\Repository;

class VehicleMake extends \Library\Repository\RepositoryAbstract {

    public $model = 'VehicleMake';

    public function get($id=null)
    {

        $this->sql = "SELECT * 
                FROM 
                  vehicle_makes WHERE 1=1 AND enabled";

        if (!empty($id))
            $this->sql .= " AND id={$id}";

        $this->sql .= " ORDER BY lower(name)";

        return $this;
    }

    public function getWithCount($id=null, $offset=0, $limit=null)
    {
        $this->offset = $offset;
        $this->limit = $limit;

        $this->sql = "SELECT make.*,
                            (SELECT count(*) 
                                FROM vehicle_years as year 
                                    JOIN vehicle_models as model ON model.vehicle_year_id=year.id 
                                WHERE year.vehicle_make_id=make.id
                            ) AS year_model_count
                        FROM 
                            vehicle_makes as make 
                            WHERE 1=1 AND enabled";

        if (!empty($id))
            $this->sql .= " AND make.id={$id}";

        $this->sql .= " ORDER BY lower(name)";

        return $this;
    }
    
}
