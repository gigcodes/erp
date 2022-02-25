@forelse($store_websites as $sw) 
<tr>
    <td>
       {{ $sw->website }} 
    </td>
    @forelse($site_development_categories as $sdc)
		<td>@if($sw->getSiteDevelopment($sw->id, $sdc->id))
                <table>
                    <tr>
                        <td> PSD Desktop
                        </td>
                        <td> PSD Mobile
                        </td>
                        <td> PSD App
                        </td>
                        <td> Figma
                        </td>
                        <td> Task
                        </td>
                    </tr>
                </table>
            @endif
        </td>
    @empty
    @endforelse
    <td>
        
</tr>
@empty
@endforelse