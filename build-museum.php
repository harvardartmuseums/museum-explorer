<?PHP 
	$apiBaseURL = "http://api.harvardartmuseums.org/collection/gallery";
	$apiKey = "105ef4e0-3c22-11e3-a9a9-797cafc83c06";
	$outputFile = "museum.json";

	unlink($outputFile);

	//Start building the museum;
	print "Building the Harvard Art Museums" . PHP_EOL;
	$museum = array("name"=>"Harvard Art Museums");

	//Build floors 1-3
	for ($i=1; $i <= 3; $i++) { 

		$url = $apiBaseURL . "?apikey=" . $apiKey . "&q=floor:". $i ."&size=50&s=gallerynumber";	
		$r = file_get_contents($url);
		$m = json_decode($r,true);

		print "Building floor " . $i . PHP_EOL;

		$floor = array("name"=>"Floor " . $i, "children"=>array());
		
		foreach ($m["records"] as $record) {
			print "Building gallery " . $record["gallerynumber"] . PHP_EOL;
			
			$url = $apiBaseURL . "/" . $record["gallerynumber"] . "?apikey=" . $apiKey;
			$r = file_get_contents($url);

			$gallery = json_decode($r, true);
			
			$gallery["children"] = $gallery["objects"];
			unset($gallery["objects"]);
			
			$floor["children"][] = $gallery;
		}
		
		$museum["children"][] = $floor;
	}

	file_put_contents($outputFile, json_encode($museum), FILE_APPEND);
	print "All set" . PHP_EOL;