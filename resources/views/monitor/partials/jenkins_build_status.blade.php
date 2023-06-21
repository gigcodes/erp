<!-- Modal -->
@php
$status = \App\Models\MonitorJenkinsBuild::where('clone_repository', '=', 0)
        ->orwhere('lock_build', '=', 0)
        ->orwhere('update_code', '=', 0)
        ->orwhere('composer_install', '=', 0)
        ->orwhere('make_config', '=', 0)
        ->orwhere('setup_upgrade', '=', 0)
        ->orwhere('compile_code', '=', 0)
        ->orwhere('static_content', '=', 0)
        ->orwhere('reindexes', '=', 0)
        ->orwhere('magento_cache_flush', '=', 0)
        ->orwhere('build_status', '=', 0)
        ->orwhere('meta_update', '=', 0)
        ->get();
 @endphp
<div id="create-jenkins-status-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">JenkinsBuild Failure Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">S.No</th>
                            <th width="5%">Project</th>
                            <th width="20%">Failure Status</th>
                        </tr>
                    </thead>
                    <tbody class="show-search-password-list">
                        @foreach($status as $key=>$stat)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$stat->project}}</td>
                            <td>{{$stat->clone_repository === 0 ? 'Clone Repository,' : ''}} 
                                {{$stat->lock_build === 0 ? 'Lock Build,' : ''}}
                                {{$stat->update_code === 0 ? 'Update Code,' : ''}}
                                {{$stat->composer_install === 0 ? 'Composer Install,' : ''}}
                                {{$stat->make_config === 0 ? 'Make Config,' : ''}}
                                {{$stat->setup_upgrade === 0 ? 'Setup Upgrade,' : ''}}
                                {{$stat->compile_code === 0 ? 'Compile Code,' : ''}}
                                {{$stat->static_content === 0 ? 'Static Content,' : ''}}
                                {{$stat->reindexes === 0 ? 'Reindexes,' : ''}}
                                {{$stat->magento_cache_flush === 0 ? 'Magento Cache Flus,' : ''}}
                                {{$stat->build_status === 0 ? 'Build Status,' : ''}}
                                {{$stat->meta_update === 0 ? 'meta_update,' : ''}}</td>
                        </tr>
                        @endforeach 
                    </tbody>
                   
                </table> 
           </div>
        </div>
    </div>
</div>