<md-card class="dashboard-card">
    <md-card-title>
        <md-card-title-text>
            <span class="md-headline">Managed Sites</span>
        </md-card-title-text>
        <md-menu md-position-mode="target-right target">
            <md-button aria-label="Action menu" class="md-icon-button" ng-click="openMenu($mdOpenMenu, $event)">
            <i class="material-icons">more_vert</i>
            </md-button>
          <md-menu-content width="4">
            <md-menu-item>
              <md-button ng-click="debugger;siteAction($event)">
                <i class="material-icons">cloud_download</i>
                Download 
              </md-button>
            </md-menu-item>
            <md-menu-item>
              <md-button ng-click="siteAction()">
                <i class="material-icons">cloud_upload</i>
                Upload
              </md-button>
            </md-menu-item>
            <md-menu-divider></md-menu-divider>
            <md-menu-item>
              <md-button ng-click="siteAction()">
                <i class="material-icons">cloud_upload</i>
              </md-button>
            </md-menu-item>
          </md-menu-content>
        </md-menu>
          
    </md-card-title>
    <md-card-content>
        <md-list flex>
            <md-list-item class="md-3-line" ng-repeat="item in sites" ng-click="null">
                <md-checkbox ng-model="item.id" aria-label="Select {{item.name}" class="md-primary"></md-checkbox>
                <img ng-src="{{item.logo_url}}?{{$index}}" class="md-avatar" alt="{{item.name}}" />
                <div class="md-list-item-text" layout="column">
                    <h3>{{ item.name }}</h3>
                    <h4>{{ item.url }}</h4>
                </div>
            </md-list-item>
        </md-list>
    </md-card-content>
</md-card>