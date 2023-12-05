@if($emailAddresses->signature_title)
Title: {!! $emailAddresses->signature_title !!}
@endif

@if($emailAddresses->signature_name)
Name: {!! $emailAddresses->signature_name !!}
@endif

@if($emailAddresses->signature_email)
Signature Email: {!! $emailAddresses->signature_email !!}
@endif

@if($emailAddresses->signature_phone)
Signature Phone: {!! $emailAddresses->signature_phone !!}
@endif

@if($emailAddresses->signature_website)
Signature Websiter: {!! $emailAddresses->signature_website !!}
@endif
