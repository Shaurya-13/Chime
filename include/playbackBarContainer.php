<?php 

$songQuery= mysqli_query($con, "SELECT song_id FROM Songs ORDER BY RAND() LIMIT 4");
$resultArray=array();
while($row = mysqli_fetch_array($songQuery)){
	array_push($resultArray, $row['song_id']);
}
$jsonArray=json_encode($resultArray);
?>
<script>
	$(document).ready (function(){

		currentPlaylist=<?php echo $jsonArray;?>;
		audioElement=new Audio();
		setTrack(currentPlaylist[0], currentPlaylist, false );
		updateVolumeProgressBar(audioElement.audio);

		$(".playBar .progressBar").mousedown(function(){
			mouseDown=true;
		});
		$(".playBar .progressBar").mousemove(function(e){
			if(mouseDown==true){
				timeFromOffset(e,this);
			}
		});
		$(".playBar .progressBar").mouseup(function(e){
			timeFromOffset(e,this);
		});

		$(".volumeBar .progressBar").mousedown(function(){
			mouseDown=true;
		});
		$(".volumeBar .progressBar").mousemove(function(e){
			if(mouseDown==true){
				var percentage=e.offsetX/$(this).width();
				if(percentage>=0 && percentage<=1){
				
				audioElement.audio.volume=percentage;
			}
			}
		});
		$(".volumeBar .progressBar").mouseup(function(e){
			var percentage=e.offsetX/$(this).width();
				if(percentage>=0 && percentage<=1){
				
				audioElement.audio.volume=percentage;
			}
			
		});

		$(document).mouseup(function(){
			mouseDown=false;
		});


	});
	function timeFromOffset(mouse, progressBar){
		var percentage=mouse.offsetX/$(progressBar).width()*100;
		var seconds=audioElement.audio.duration*(percentage/100);
		audioElement.setTime(seconds);
	}
	function prevSong(){
		if(audioElement.audio.currentTime>=3||currentIndex==0){
			audioElement.setTime(0);
		}
		else{
			currentIndex=currentIndex-1;
			setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
		}
	}

	function nextSong(){
		if(repeat==true){
			audioElement.setTime(0);
			playSong();
			return;
		}
		if(currentIndex==currentPlaylist.length-1){
			currentIndex=0;
		}
		else{
			currentIndex=currentIndex+1;
		}
		var trackToPlay=currentPlaylist[currentIndex];
		setTrack(trackToPlay,currentPlaylist, true);
	}
	function setRepeat(){
		repeat=!repeat;
		var imageName=repeat ? "repeat-active.png":"repeat.png" /*different way of if else statement*/
		$("butControl.repeat img").attr("src","assets/images/icons/"+imageName);
	}

	
	function setTrack(trackId, newPlaylist, play){
		currentIndex=currentPlaylist.indexOf(trackId);
		pauseSong();

		$.post("include/handlers/ajax/getSongJson.php",{ songId:trackId }, function(data){
			
			var track=JSON.parse(data);
			$(".songName span").text(track.title);

			$.post("include/handlers/ajax/getArtistJson.php",{ artistId:track.artist }, function(data){
				var artist=JSON.parse(data);
				$(".authorName span").text(artist.name);
			});
			$.post("include/handlers/ajax/getAlbumJson.php",{ albumId:track.album }, function(data){
				var album=JSON.parse(data);
				$(".album img").attr("src",album.artworkPath);
			});
			audioElement.setTrack(track);
			playSong();
		});
		if(play==true){
			audioElement.play();
		}
	}
	function playSong(){
		if(audioElement.audio.currentTime==0){
			$.post("include/handlers/ajax/updatePlays.php",{songId: audioElement.currentlyPlaying.song_id});
		}
		
		
		$(".butControl.play").hide();
		$(".butControl.pause").show();
		audioElement.play();
	}
	function pauseSong(){
		$(".butControl.play").show();
		$(".butControl.pause").hide();
		audioElement.pause();
	}
	
</script>
<div id="playbackBarContainer">

			<div id="playbackBar">

				<div id="playbackBarLeft">
					<div class="bar">
						<span class="album">
							<img src="" class="artwork">
						</span>

						<div class="songInfo">
								
							<span class="songName">
								<span></span>
							</span>

							<span class="authorName">
								<span></span>
							</span>
							
						</div>

					</div>

				</div>

				<div id="playbackBarCenter">

					<div class="controls bar">
						
						<div class="button">

							

							<button class="butControl previous" title="Previous button" onclick="prevSong()">
								<img src="assets/images/icons/previous.png" alt="Previous">
							</button>

							<button class="butControl play" title="Play button" onclick="playSong()">
								<img src="assets/images/icons/play.png" alt="Play">
							</button>

							<button class="butControl pause" title="Pause button" onclick="pauseSong()" style="display:none;">
								<img src="assets/images/icons/pause.png" alt="Pause">
							</button>

							<button class="butControl next" title="Next button" onclick="nextSong()">
								<img src="assets/images/icons/next.png" alt="Next">
							</button>

							<button class="butControl repeat" title="Repeat button" onclick="setRepeat()">
								<img src="assets/images/icons/repeat.png" alt="Repeat">
							</button>

						</div>


						<div class="playBar">
							
							<span class="time current">0.00</span>

							<div class="progressBar">
								<div class="progressBarBG">
									<div class="progress"></div>
								</div>
							</div>

							<span class="time remaining">0.00</span>


						</div>


					</div>

				
				</div>

				<div id="playbackBarRight">
					<div class="volumeBar">

						<button class="butControl volume" title="Volume button">
							<img src="assets/images/icons/volume.png" alt="Volume">
						</button>

						<div class="progressBar">
								<div class="progressBarBG">
									<div class="progress"></div>
								</div>
						</div>

					</div>
				</div>

			</div>