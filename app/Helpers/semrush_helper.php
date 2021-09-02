<?php

function parseSemrushResponse($response) {
		$response1 = explode("\n", $response);
		$final = [];
		foreach($response1 as $new) {
			$new = explode(';', $new);
			$final[] = $new;
		}
		return json_encode($final);
	}