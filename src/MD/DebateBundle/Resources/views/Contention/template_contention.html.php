<div class="debate-section section-contention clearfix">
    <h3 class="section-title">Contentions</h3>
    <div class="section-content clearfix">

        <div ng-controller="contentionGroupCtrl" ng-repeat="(contentionType, contentionGroup) in debate.contentions_sorted" class="contention {[{contentionType}]}-section">
            <a class="button contention-new" ng-click="toggleEditForm()">+ Add New Contention</a>
            <div class="contention-new" ng-show="editing">
                <ng-include src="getPath(debate.id, contentionType)"></ng-include>
            </div>
            <div ng-controller="contentionCtrl" ng-repeat="contention in contentionGroup" class="contention" id="contention-{[{ contention.id }]}">
                <div class="contention-heading">
                    <h2 class="contention-name">{[{ contention.name }]}</h2>
                    <div class="new-point"><a class="button" href="{[{ path('md_debate_point_new', {'did': debate.id, 'cid': contention.id}) }]}" title="Expand on this Point">+ Add a Point</a></div>
                </div>

                <div class="points" ng-show="contention.points">
                    <?php echo $view->render(
                        'MDDebateBundle:Point:template_point.html.php'
                    ) ?>
                </div>
            </div>
        </div>
    </div>
</div>