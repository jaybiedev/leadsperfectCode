<?php
namespace Library\Widgets\Leads;

class AccountSitesDD extends \Library\Widgets\WidgetsAbstract {
    
    protected $CI;
    protected $db;

    public function __construct($Account=null)
    {
        $this->CI =& get_instance();
        $this->db = $this->CI->db;
        $this->Account = $Account;
        $this->elementID = 'AccountSitesDD';
    }

    public function render() {
        echo $this->getContent();
    }
    
    public function getContent() {

        $SiteRepository = new \Library\Repository\Leads\Site();
        $Sites = $SiteRepository->getByAccount($this->Account->id, $orderby="UPPER(state), UPPER(city)")->getArray();
        
        $content =<<<JS
<script>
function filterAccountSitesDD(sInput) {
    var filter = sInput.value.toUpperCase();
    a = sInput.parentElement.getElementsByTagName("a");
    for (i = 0; i < a.length; i++) {
        if (a[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
          a[i].style.display = "";
        } else {
          a[i].style.display = "none";
        }
    }
}
</script>
JS;
        
        $content .='<div class="widget dd" id="'. $this->elementID .'">
        <div class="btn-group">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Find other churches
            </button>
            <div class="dropdown-menu">
                <input type="text" placeholder="Search" onkeyup="filterAccountSitesDD(this)" />';
            
        foreach ($Sites as $Site) {
            $content .= '<a class="dropdown-item" href="/'. $Site->slug .'">'   . $Site->city . ' ' . $Site->state . ',  ' . $Site->name . '</a>';
        }
                
        $content .= '</div>
        </div></div>';        
        return $content;
    }

}
