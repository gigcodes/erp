<div class="modal-header">
    <h3>Site Audit</h3>
    <button type="button" class="close" data-dismiss="modal">×</button>
</div>
<div class="table-responsive" >	
	<ul>
		<li>Name: {{$siteAudit['name']}}</li>
		<li>status: {{$siteAudit['status']}}</li>
		<li>errors: {{$siteAudit['errors']}}</li>
		<li>warnings: {{$siteAudit['warnings']}}</li>
		<li>notices: {{$siteAudit['notices']}}</li>
		<li>broken: {{$siteAudit['broken']}}</li>
		<li>blocked: {{$siteAudit['blocked']}}</li>
		<li>redirected: {{$siteAudit['redirected']}}</li>
		<li>healthy: {{$siteAudit['healthy']}}</li>
		<li>haveIssues: {{$siteAudit['haveIssues']}}</li>
		<li>haveIssuesDelta: {{$siteAudit['haveIssuesDelta']}}</li>
		<li>defects: {{$siteAudit['defects']}}</li>
		<li>markups: {{$siteAudit['markups']}}</li>
		<li>depths: {{$siteAudit['depths']}}</li>
		<li>crawlSubdomains: {{$siteAudit['crawlSubdomains']}}</li>
		<li>respectCrawlDelay: {{$siteAudit['respectCrawlDelay']}}</li>
		<li>canonical: {{$siteAudit['canonical']}}</li>
		<li>user_agent_type: {{$siteAudit['user_agent_type']}}</li>
		<li>last_audit: {{$siteAudit['last_audit']}}</li>
		<li>last_failed_audit: {{$siteAudit['last_failed_audit']}}</li>
		<li>next_audit: {{$siteAudit['next_audit']}}</li>
		<li>running_pages_crawled: {{$siteAudit['running_pages_crawled']}}</li>
		<li>running_pages_limit: {{$siteAudit['running_pages_limit']}}</li>
		<li>pages_crawled: {{$siteAudit['pages_crawled']}}</li>
		<li>pages_limit: {{$siteAudit['pages_limit']}}</li>
		<li>total_checks: {{$siteAudit['total_checks']}}</li>
		<li>errors_delta: {{$siteAudit['errors_delta']}}</li>
		<li>warnings_delta: {{$siteAudit['warnings_delta']}}</li>
		<li>notices_delta: {{$siteAudit['notices_delta']}}</li>
		<li>mask_allow: {{$siteAudit['mask_allow']}}</li>
		<li>mask_disallow: {{$siteAudit['mask_disallow']}}</li>
		<li>removedParameters: {{$siteAudit['removedParameters']}}</li>
		<li>excluded_checks: {{$siteAudit['excluded_checks']}}</li>
	</ul>
</div>