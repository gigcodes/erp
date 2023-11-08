<form action="<?php echo route('virtualmin.domains.dnsupdate'); ?>">
    <input type="hidden" name="id" value="{{ $VirtualminDomainDnsRecords->id }}">
    @csrf
    @method('POST')
    <div class="modal-body">
        <div class="form-group">
            {!! Form::label('ip_address', 'Content', ['class' => 'form-control-label']) !!}
            {!! Form::text('ip_address', $VirtualminDomainDnsRecords->content, ['class'=>'form-control','required']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('name', 'DNS Name', ['class' => 'form-control-label']) !!}
            {!! Form::text('name', $VirtualminDomainDnsRecords->name, ['class'=>'form-control','required']) !!}
            {!! Form::hidden('Virtual_min_domain_id', $VirtualminDomainDnsRecords->Virtual_min_domain_id) !!}
            {!! Form::hidden('dns_type', 'TXT') !!}  
            {!! Form::hidden('type', $VirtualminDomainDnsRecords->type) !!}
            {!! Form::hidden('proxied', 2) !!}       
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary a-dns-update-btn">Update</button>
        </div>
    </div>
</form>
