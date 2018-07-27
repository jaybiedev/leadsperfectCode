<md-card class="dashboard-card">
	<md-card-title>
        <md-card-title-text>
            <span class="md-headline">Statistics</span>
        </md-card-title-text>
    </md-card-title>
    <md-card-content>
    	<canvas id="doughnut" class="chart chart-doughnut"
  chart-data="data" chart-labels="labels">
</canvas>
<canvas id="bar" class="chart chart-bar"
  chart-data="data2" chart-labels="labels2"> chart-series="series2"
</canvas>
    </md-card-content>
</md-card>