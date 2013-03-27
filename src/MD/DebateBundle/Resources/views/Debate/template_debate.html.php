<div ng-show="debate.editable">
    <a class="button edit" ng-click="toggleEditForm(debate.id)">Edit</a>
</div>
<div class="debate-header" ng-hide="editing">
    <div class="debate-title-wrapper">
        <h1 class="debate-title" id="debate-{[{ debate.id }]}">{[{ debate.name }]}</h1>
    </div>
    <div class="debate-description" >
        {[{ debate.description }]}
    </div>
</div>
<div ng-show="debate.editable">
    <div ng-show="editing" class="form debate-form">
        <div class="debate-header" ng-show="editing">
            <ng-include src="getPath(debate.id)"></ng-include>
        </div>
    </div>
</div>

<div ng-show="debate.contentions_sorted" class="debate-contentions" >
    <?php echo $view->render(
        'MDDebateBundle:Contention:template_contention.html.php'
    ) ?>
</div>
