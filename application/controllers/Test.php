<?php

class Test extends Library\MainController
{
    public function index()
    {
        // disabled framework autoloaders for unitest
        $autoloadFuncs = spl_autoload_functions();

        foreach($autoloadFuncs as $unregisterFunc)
        {
            spl_autoload_unregister($unregisterFunc);
        }

        chdir(APPPATH . "/third_party/VisualPHPUnit");
        require_once(APPPATH . "/third_party/VisualPHPUnit/app/public/index.php");
    }
}