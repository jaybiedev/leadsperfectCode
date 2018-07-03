<?php

class Upgrade extends Library\MainController {

    public function index()
    {
        // check first if upgrade table
        if ($this->db->table_exists('upgrade') == false) {
            $this->createUpgradeTable();
        }

        // @todo: define bundle as configurable
        $bundle = 'JGM';

        $upgrades_dir = APPPATH . "bundle/{$bundle}/upgrades/";
        $upgrades = glob($upgrades_dir . '*');

        $ran = [];

        foreach ($upgrades as $upgrade) {

            $upgrade_basename = basename($upgrade);

            $this->db->flush_cache();
            $this->db->where('upgrade', $upgrade_basename);
            $this->db->where('success', true);
            $this->db->from('upgrade');

            if ($this->db->count_all_results() == 0) {
                $ran[] = $upgrade_basename;

                $pathinfo = pathinfo($upgrade_basename);
                if ($pathinfo['extension'] == 'sql')
                    $success = $this->executeSQLUpgrade($upgrade);
                else
                    $success = $this->executePHPUpgrade($upgrade);


                // @todo: notification if process fails
                $data = array(
                    'upgrade' => $upgrade_basename,
                    'success' => $success,
                );

                $this->db->flush_cache();
                $this->db->from('upgrade');
                $this->db->where('upgrade', $upgrade_basename);

                if ($this->db->count_all_results() == 0) {
                    $this->db->insert('upgrade', $data);
                }
                else {
                    $this->db->replace('upgrade', $data);
                }

            }
        }

        pprint_r($ran);
        echo "<br />Upgrade done.";

    }


    private function executeSQLUpgrade($file) {

        $sql_content = file_get_contents($file);

        $this->db->trans_start();
        // run as the whole contents such as in functions
        if (strpos($file, 'AS_FILE') !== false)
        {
            $this->db->query($sql_content);
        }
        else
        {
            // break up sqls
            $sqls = preg_split("/;/", trim($sql_content), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($sqls AS $sql) {
                $this->db->query($sql);
            }
        }
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    private function executePHPUpgrade($file) {
        //
    }


    private function createUpgradeTable() {
        $sql = "CREATE TABLE IF NOT EXISTS upgrade (
                id SERIAL,
                upgrade text NOT NULL,
                date_processed timestamp without time zone DEFAULT now(),
                success boolean DEFAULT true,
                log text
            );";
        $this->db->query($sql);

        $sql = "CREATE UNIQUE INDEX IF NOT EXISTS upgrade_name_ukey ON upgrade USING btree (upgrade);";
        $this->db->query($sql);
    }
}
