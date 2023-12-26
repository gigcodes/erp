<?php
                    $isReviwerLikeAdmin = auth()
                        ->user()
                        ->isReviwerLikeAdmin();
                    $userID = Auth::user()->id;
                    ?>
@foreach ($issues as $key => $issue)
    @if (true || $isReviwerLikeAdmin)
        @include("development.partials.summarydata")
    @elseif($issue->created_by == $userID || $issue->master_user_id == $userID ||
        $issue->assigned_to == $userID)
        @include("development.partials.developer-row-view-s")
    @endif
@endforeach