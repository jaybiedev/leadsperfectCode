<md-card class="dashboard-card">
                    <md-card-title>
                        <md-card-title-text>
                            <span class="md-headline">Information</span>
                        </md-card-title-text>
                    </md-card-title>
                    <md-card-content ng-if="false" style="height:600px;overflow:auto;">
                        <template-iframe></template-iframe>
                    </md-card-content>
                    <md-card-content>
                    	<md-list flex>
                            <md-list-item class="md-3-line">
                                <div class="md-list-item-text" layout="column">
                                    <h3>{{ Data.Template.name }}
                                    <i class="material-icons" title="Edit template" alt="Edit template" 
                                	ng-click="siteAction('maangesite', '{{ item.guid }}')">edit</i>
                                	</h3>
                                    <p>Edited: {{ Data.Template.date_modified }}
                                    </p>
                                </div>
                            </md-list-item>
                        </md-list>                        
                        <h4>Template Variables / Default Values</h4>
                        <md-content layout-padding style="height:350px">
                            <div>
                              <form name="template-tags-frm">
                              	<md-input-container class="md-block"  ng-repeat="tag in Data.ContentTags track by $index">
                                  <label>{{ tag.name }} | {{ tag.tag }} </label>
                                  <input ng-model="Data.ContentTags[$index +1].default_value" value="{{ tag.default_value }} ">
                                </md-input-container>
                	            </form>
                            </div>
                        </md-content>
                        <div>
                            <md-button type="submit">Submit</md-button>
                        </div>
                    </md-card-content>
                </md-card>