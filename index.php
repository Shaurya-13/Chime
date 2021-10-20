 
<?php include("include/header.php"); ?>
<h1 class="pageHeading">Some Good Music</h1>
			<div class="gridView">
			
				<?php

				$albumQuery = mysqli_query($con,"SELECT * FROM albums ORDER BY RAND() LIMIT 10");

				while($row=mysqli_fetch_array($albumQuery)){


					
					echo "<div class='gridViewItem'>
						<a href='album.php?albumId=" . $row['albumId'] . "'>
							<img src='" . $row['artworkPath'] . "'>

							<div class='gridViewInfo'>"
								. $row['title'] . 
							"</div>
						</a>
					</div>";

				}
				
				
				?>

<?php include("include/footer.php");?>	