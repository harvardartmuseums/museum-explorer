<?PHP 
	$apiBaseURL = "http://api.harvardartmuseums.org";
	$apiKey = "105ef4e0-3c22-11e3-a9a9-797cafc83c06";
	$outputFile = "museum.json";

	unlink($outputFile);

	//Start building the museum;
	print "Building the Harvard Art Museums" . PHP_EOL;
	$museum = array("name"=>"Harvard Art Museums");
	
	//Build floors 1-3
	for ($i=0; $i <= 5; $i++) { 

		$url = $apiBaseURL . "/gallery?apikey=" . $apiKey . "&q=floor:". $i ."&size=50&sort=gallerynumber";	
		$r = file_get_contents($url);
		$m = json_decode($r,true);

		print "Building floor " . $i . PHP_EOL;

		$floor = array("name"=>"Level " . $i, "children"=>array());
		
		foreach ($m["records"] as $record) {
			print "Building gallery " . $record["id"] . PHP_EOL;
			
			$url = $apiBaseURL . "/gallery/" . $record["id"] . "?apikey=" . $apiKey;
			$r = file_get_contents($url);
			$gallery = json_decode($r, true);
			unset($gallery["contains"]);
			unset($gallery["lastupdate"]);
			unset($gallery["id"]);

			$url = $apiBaseURL . "/object?apikey=" . $apiKey . "&size=200&fields=objectnumber,title,primaryimageurl,classification,dated,url&gallery=" . $record["gallerynumber"];			
			$r = file_get_contents($url);
			$objects = json_decode($r, true);

			$gallery["objectcount"] = count($objects["records"]);
			$gallery["children"] = $objects["records"];
			
			$floor["children"][] = $gallery;
		}
		
		$museum["children"][] = $floor;
	}

	file_put_contents($outputFile, json_encode($museum), FILE_APPEND);
	print "All set" . PHP_EOL;