@foreach ($issues as $key => $issue)
    @if (auth()->user()->isReviwerLikeAdmin())
        @include("development.partials.summarydata")
    @elseif($issue->created_by == auth()->user()->id || $issue->master_user_id == auth()->user()->id ||
        $issue->assigned_to == auth()->user()->id)
        @include("development.partials.developer-row-view-s")
    @endif
@endforeach
