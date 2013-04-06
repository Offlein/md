<div class="points" ng-show="contention.points" ng-controller="pointCtrl">
    <div class="point-new" ng-show="editing">
        <ng-include src="getPath(debate.id, 'new', contentionType)"></ng-include>
    </div>
    <div ng-repeat="point in contention.points" class="point" id="point-{[{ point.id }]}">
        <span class="point-body">{[{ point.body }]}</span>
        <span class="point-source">{[{ point.source }]}</span>
        <div class="point-created">({[{ point.created }]})</div>
    </div>
</div>