
use Illuminate\Support\Facades\Route;
@if(setting('app-custom-head') && \Route::currentRouteName() !== 'settings')
    <!-- Custom user content -->
    {!! setting('app-custom-head') !!}
    <!-- End custom user content -->
@endif