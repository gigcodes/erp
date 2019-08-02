<html>
<head>
    <title>Scraper vs AI Results</title>
    <link rel="stylesheet" href="http://erp.sololuxury.mac/css/bootstrap.min.css">
</head>

<body>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="page-heading">Scraper vs AI Results</h1>
        </div>
        @foreach($results as $key=>$item)
            @if ($item)
                <div class="row">
                    <div class="col-md-12">
                        <h3>{{ date('d-m-Y H:i', strtotime($item->created_at)) }} Scraper vs {{ $item->ai_name }}</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        @php
                            $images = json_decode($item->media_input);
                        @endphp
                        @foreach( $images as $image )
                            <img src="{{ $image }}" style="height: 200px; width: auto;">
                        @endforeach
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h4>Scraper</h4>
                        @php
                            $resultScraper = json_decode($item->result_scraper);
                        @endphp
                        @foreach( $resultScraper as $resultKey=>$resultValue)
                            {{ $resultKey }}: <b>{{ $resultValue }}</b><br/>
                        @endforeach
                    </div>
                    <div class="col-md-6">
                        <h4>AI</h4>
                        @php
                            $resultAi = json_decode($item->result_ai);
                        @endphp
                        @foreach( $resultAi as $resultKey=>$resultValue)
                            {{ $resultKey }}: <b>{{ $resultValue }}</b><br/>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

</body>

</html>