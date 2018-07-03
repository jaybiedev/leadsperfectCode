<?
        function glayer($layer, $content) {
                global $g;
                $g->objResponse->addAssign($layer, 'style.display', 'block');
                $g->objResponse->addAssign($layer, 'innerHTML', $content);
        }
        
        function hide_layer($layer) {
                global $g;
                $g->objResponse->addAssign($layer, 'style.display', 'none');
        }
        
        function show_layer($layer) {
                global $g;
                $g->objResponse->addAssign($layer, 'style.display', 'block');
        }
        
        function done() {
                global $g;
                hide_layer('wait.layer');
                return $g->objResponse->getXML();
        }
        
  		  function galert($m)
		  {
				global $g;
				$g->objResponse->addAlert($m);
		   }
      
        function prompt($m)
        {
        		global $g;
        		$v = $g->objResponse->addScript("var x=prompt(\"$m\");");
        		return $v;
        }
        
        function gset($element, $value) {
                global $g;
                 $g->objResponse->addAssign($element, 'value', $value);
        }
		  function gscript($m)
		  {
	  		  	global $g;
		  		$v = $g->objResponse->addScript($m);
		  		return $v;
		  }

?>