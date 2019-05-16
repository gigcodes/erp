<?php

namespace App\Services\Instagram;


use App\TargetLocation;

class Location {

    public function pointInPolygon($x,$y) {
        $latLng = TargetLocation::all();
        foreach ($latLng as $item) {

            $polyX = $item->region_data[0];
            $polyY = $item->region_data[1];

            $polySides = count($polyX);
            $j = $polySides-1 ;
            $oddNodes = 0;
            for ($i=0; $i<$polySides; $i++) {
                if (($polyY[$i]<$y && $polyY[$j]>=$y) ||  ($polyY[$j]<$y && $polyY[$i]>=$y)) {
                    if ($polyX[$i]+($y-$polyY[$i])/($polyY[$j]-$polyY[$i])*($polyX[$j]-$polyX[$i])<$x) {
                        $oddNodes=!$oddNodes;
                    }
                }
                $j=$i;
            }

            if ($oddNodes) {
                return [$oddNodes, $item];
            }
        }

        [0, null];

    }
}
