<!DOCTYPE html>
<?php
    $key = "***********";
	$url_collections = "https://objecthub.keio.ac.jp/open_koh/v1/collection?api_key=" . $key;
	$json = file_get_contents($url_collections);
	$arr = json_decode($json,true);
?>
<html>
  <head>
    <meta charset='utf-8'>
    <link href="iiif_viewer.css" type="text/css" rel="stylesheet">
    <script src="jquery-3.2.1.min.js"></script>  
    <script src="openseadragon-bin-4.1.0/openseadragon.min.js"></script> 
  </head>
  <body>
    <div class="title"><img src="ichigoichie.png"></div>
  <h3>コレクションを選択</h3>
		<form action="#" method="post">
		<div class="cp_ipselect cp_sl03">
				<select  onchange="submit(this.form)" name="collection">
					<option value="" hidden>Choose</option>
					<?php
						for($i = 0 ; $i < $arr["count"] ; $i++){
              if($i == 22){
                continue;
              } else if($i == 17){
                continue;
              } else if($i == 12){
                continue;
              }
							echo "<option value=" . $arr["data"][$i]["id"] . " id=". $arr["data"][$i]["id"] .">" . $arr["data"][$i]["collection_title"]["jp"] . "</option>";
						}
					?>
				</select>
				</div>
		</form>
    <?php
			$url = "https://objecthub.keio.ac.jp/open_koh/v1/collection?data_id=".$_POST['collection']."&api_key=" . $key;
			$json = file_get_contents($url);
    		$arr = json_decode($json,true);
			echo "<h4>選択中：".$arr["data"][0]["collection_title"]["jp"]."</h4>";
    		$rand_key = array_rand($arr["data"][0]["objects"], 5);
			$paths = array();
			$work_ids = $arr["data"][0]["objects"][$rand_key[0]]["id"].",".$arr["data"][0]["objects"][$rand_key[1]]["id"].",".$arr["data"][0]["objects"][$rand_key[2]]["id"].",".$arr["data"][0]["objects"][$rand_key[3]]["id"].",".$arr["data"][0]["objects"][$rand_key[4]]["id"];
			$url = "https://objecthub.keio.ac.jp/open_koh/v1/object?data_id=".$work_ids."&api_key=" . $key;
			$json = file_get_contents($url);
			$arr = json_decode($json, true);
			for($j = 0 ; $j < 5 ; $j++){
				$paths[$j] = $arr["data"][$j]["iiif_manifest"]["2.1"];
			}
		?>
    
    <div class="works">
        <?php
            for($j = 0 ; $j < 5 ; $j ++){
				echo '<div class="card pic-image" id="anchor'.$j.'">';
                echo '<img class="card-image" src="'.$arr["data"][$j]["images"][0]["url"]["large"].'" alt="">';
				echo '<div class="card-box">';
                echo '<h2 class="card-title">'.$arr["data"][$j]["title"]["jp"].'</h2>';
                echo '<p class="card-description"><a href='.$arr["data"][$j]["kohurl"]["jp"].' class="cp_textlink06">KOHでもっと詳しく見る</a></p>';
                echo '</div>';
				echo '</div>';
            }
        ?>
		</div>
    <div id="myViewer" class="openseadragon" style="width:80%;height:800px;margin: 0 auto;"></div>
    
    <script>
    window.addEventListener('load',function(){
    var URLs = JSON.parse('<?php echo json_encode($paths)?>');
    console.log(URLs);
    var i = 0;
    arIJsonUri = [];
    arNum = [];
    for(i = 0 ; i < 5 ; i ++){
      var pUrl = URLs[i];
      if(pUrl.slice(0,5) != "https"){
        var head = pUrl.slice(0,4);
        var body = "s";
        var tail = pUrl.slice(4);
        pUrl = head + body + tail;
      }
    
     $.ajax({url:pUrl,dataType:'json'}).done(function(maniJson){
         const iJsonUri = maniJson["sequences"][0]["canvases"][0]["images"][0]["resource"]["service"]["@id"]+'/info.json';
         arIJsonUri.push(iJsonUri);
         viewer.open(arIJsonUri);
     });
    }
     console.log(arIJsonUri);
    
   });
   var viewer = OpenSeadragon({
    id: "myViewer",
    prefixUrl:     "openseadragon-bin-4.1.0/images/",
    visibilityRatio:    1,
    minZoomLevel:       0.3,
    defaultZoomLevel:   0.7,
    sequenceMode:  true,
      initialPage: 0,
      tileSources:   [  ]
    });
    </script>
  </body>
</html>