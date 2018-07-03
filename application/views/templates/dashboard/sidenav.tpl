<md-toolbar class="main-header md-whiteframe-3dp">
    <div class="md-toolbar-tools">
        <h1 flex>Dashboard</h1>
    </div>
</md-toolbar>


<div class="main-container" layout="row" flex>
    <md-sidenav class="main-sidenav md-sidenav-left" md-component-id="navigation-drawer" md-is-locked-open="true" layout="column">
        <md-content md-scroll-y flex layout="column">
            <md-sidemenu locked="true">
                <md-sidemenu-group>
                    <md-subheader class="md-no-sticky">Caption</md-subheader>

                    <md-sidemenu-content md-icon="home" md-heading="Menu 1" md-arrow="true">
                        <md-sidemenu-button href="#">Submenu 1</md-sidemenu-button>
                        <md-sidemenu-button href="#">Submenu 2</md-sidemenu-button>
                        <md-sidemenu-button href="#">Submenu 3</md-sidemenu-button>
                    </md-sidemenu-content>
                </md-sidemenu-group>

                <md-sidemenu-group>
                    <md-divider></md-divider>

                    <md-subheader class="md-no-sticky">Caption</md-subheader>

                    <md-sidemenu-button href="#">Menu 4</md-sidemenu-button>
                </md-sidemenu-group>
            </md-sidemenu>
        </md-content>
    </md-sidenav>

    <md-content class="main-content" md-scroll-y layout="column" flex>

    </md-content>
</div>