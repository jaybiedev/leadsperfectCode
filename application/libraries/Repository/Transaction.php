<?php

namespace Library\Repository;

class Transaction extends \Library\Repository\RepositoryAbstract {

    public $model = 'Transaction';

    public function get($id=null, $transaction_status=null, $transaction_type=null, $offset=0, $limit=null)
    {
        $this->offset = $offset;
        $this->limit = $limit;

        $this->sql = "SELECT
                        trh.*,
                        acc.first_name AS account_first_name,
                        acc.last_name AS account_last_name,
                        trt.name AS transaction_type_name,
                        trs.name AS transaction_status_name,
                        vhy.year AS vehicle_year,
                        vhk.name AS vehicle_make,
                        vhm.name AS vehicle_model,
                        usr.username AS username
                FROM
                    transaction_header trh
                LEFT JOIN
                    account AS acc ON acc.id=trh.account_id
                LEFT JOIN
                    vehicle_models AS vmd ON vmd.id=trh.vehicle_model_id
                LEFT JOIN
                    transaction_type AS trt ON trt.id=trh.transaction_type_id
                LEFT JOIN
                    transaction_status trs ON trs.id=trh.transaction_status_id
                LEFT JOIN
                    vehicle_models AS vhm ON vhm.id=trh.vehicle_model_id
                LEFT JOIN
                    vehicle_years AS vhy ON vhy.id=vhm.vehicle_year_id
                LEFT JOIN
                    vehicle_makes AS vhk ON vhk.id=vhy.vehicle_make_id
                LEFT JOIN
                    users AS usr ON usr.id=trh.addedby_user_id
                  
                WHERE 1=1 ";

        if (!empty($id)) {
            $this->sql .= " id=" . intval($id);
        }
        else {

            if (!empty($transaction_type))
                $this->sql .= "\n AND transaction_type.system_name='{$transaction_type}' ";
            if (!empty($transaction_status))
                $this->sql .= "\n AND transaction_status.system_name='{$transaction_status}' ";


            $this->sql .= " ORDER BY date_added DESC";
        }

        return $this;
    }
}

