<h2>Account Statistics</h2>
<md-content class="md-padding" layout-xs="column" layout="row">
    <div flex flex-gt-xs="50" layout="column">
    <md-card class="dashboard-card chart">
        <md-card-title>
            <md-card-title-text>
                <span class="md-headline">Visits</span>
            </md-card-title-text>
        </md-card-title>
        <md-card-content>
            <canvas id="line" class="chart chart-line" 
          chart-options="Data.Charts.Views.options"
          chart-labels="Data.Charts.Views.labels" 
              chart-data="Data.Charts.Views.data"
          chart-series="Data.Charts.Views.series" 
          chart-dataset-override="Data.Charts.Views.datasetOverride"
          chart-click="Data.Charts.Views.onClick">
          </canvas>
        </md-card-content>    
    </md-card>
  </div>  
    <div flex-xs flex-gt-xs="50" layout="column">
    <md-card class="dashboard-card chart">
        <md-card-title>
            <md-card-title-text>
                <span class="md-headline">Actions</span>
            </md-card-title-text>
        </md-card-title>
        <md-card-content>
           <canvas id="base" class="chart-horizontal-bar"
          chart-data="Data.Charts.Actions.data"
           chart-labels="Data.Charts.Actions.labels" >
        </canvas> 
        </md-card-content>    
    </md-card>
  </div>  
</md-content>