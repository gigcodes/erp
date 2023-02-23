<style type="text/css">
    .green-notification {
        color: green;
    }

    .red-notification {
        color: grey;
    }
</style>
<table class="table table-bordered table-striped" style="table-layout:fixed;">
    <tr>
        <th style="width:12%;">ID</th>
        <th style="width:5%;">Module</th>
        <th style="width:5%;">Date</th>
        <th style="width:8%;">Subject</th>
        <th style="width:20%;">Communication</th>
        <th style="width:10%;">Est Completion Time</th>
        <th style="width:10%;">Est Completion Date</th>
        <th style="width:9%;">Tracked Time</th>
        <th style="width:13%;">Developers</th>
        <th style="width:10%;">Status</th>
        <th style="width:5%;">Cost</th>
        <th style="width:7%;">Milestone</th>
        <th style="width:10%">Estimated Time</th>
        <th style="width:10%">Estimated Start Datetime</th>
        <th style="width:10%">Estimated End Datetime</th>
        <th style="width:7%;">Actions</th>
    </tr>
    <?php
    $isReviwerLikeAdmin = auth()->user()->isReviwerLikeAdmin();
    $userID = Auth::user()->id;
    ?>
    <?php foreach ($issues as $key => $issue) { ?>
        <?php if ($isReviwerLikeAdmin) { ?>
            @include("development.partials.admin-row-view")
        <?php } elseif ($issue->created_by == $userID || $issue->master_user_id == $userID || $issue->assigned_to == $userID || $issue->team_lead_id == $userID || $issue->tester_id == $userID) { ?>
            @include("development.partials.developer-row-view")
        <?php } ?>
    <?php } ?>
</table>