<style>
  .play-title{
    font-size: 15px !important;
	  color : #0B0B0B;
  }
</style>
<script>
	$(document).ready(function(){
		$('.progress').hide(); // hide class 1st
		$('#btn_write').click(function () {
			if ($('.progress').is(':hidden')) {
				$('.progress').show();
			} else {
				$('.progress').hide();
			}
		});
	});
</script>
<section class="lobby">
	<a style="
    padding-top: 6px;
    padding-bottom: 6px;
    padding-left: 12px;
    padding-right: 12px;
" href="<?php echo base_url('game') ?>" class="float-right btn btn-outline-red btn-md">
		<i class="fa fa-play"></i> <?php echo $this->lang->line('playgame'); ?></a>
	<span class="mb-4 mt-1" style="font-size: 25px"><i class="fa fa-gamepad"></i>&nbsp;
		<?php 
		echo empty($game_name) ? "" : " ".$game_name; 
		?>
	</span>
	<button id="btn_write" type="button" class="btn btn-info " style="
	  background-color: #4CAF50;
	  border: none;
	  color: white;
	  padding: 15px 25px;
	  text-align: center;
	  text-decoration: none;
	  display: inline-block;
	  font-size: 16px;">
		<?php echo $this->lang->line('winrate'); ?> :
	</button>
	<hr style="margin-top: 15px">
</section>
<div class="page-content" >
  <div  class="" style="">
	  <div class="row mt-3 mb-0 text-center" >
		  <?php foreach($game_list as $data_game): ?>
			<div class="col-3 col-sm-2 pl-1 pr-1">
				<?php
					$percentage = $_SESSION['percentage_'.$game_code][$data_game['gameId']]; // $agent_name should be"pg_game"oranything else
					$game_type = strtolower($data_game['gameType']);
					if(isset($data_game['gameName'])){
						$game_name =$data_game['gameName'];
					}else if(isset($data_game['name'])){
						$game_name =$data_game['name']['th'];
					}
					$imgUrl = "";
					if(isset($data_game['imgUrl'])){
						$imgUrl =$data_game['imgUrl'];
					}else if(isset($data_game['thumbnail'])){
						$imgUrl =$data_game['thumbnail'];
					}
				?>
				<?php if((isset($data_game['isActivated']) && $data_game['isActivated']) || isset($data_game['isActive']) && $data_game['isActive']): ?>
				  <a target="_blank" href="<?php echo site_url('/home/play_game/'.$game_code.'/'.$data_game['gameId'].'?title='.$game_name) ?>" data-name="<?php echo $game_name; ?>" data-type="<?php echo $game_code; ?>" class="btn-play-game">
					<div class="card-game box-shadow-3 hvr-grow">
					  <div class="card-body p-1">
						<img class="img-fluid rounded img-game" src="<?php echo $imgUrl; ?>" />
						<h5 class="play-title mt-0 mb-0"><?php echo $game_name; ?></h5>
						<?php if($game_type == "slot" || $game_type == "table"): ?>
						   <div class="progress">
								  <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"><?php echo $percentage; ?></div>
							</div>
						<?php endif; ?>
					  </div>
					</div>
				  </a>
				<?php endif; ?>
			</div>
		  <?php endforeach; ?>
      </div>
  </div>
</div>
