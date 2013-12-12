<?php
  // Yii::app()->clientScript->registerCoreScript('jquery');

  $baseUrl = Yii::app()->request->baseUrl;
  $cs = Yii::app()->getClientScript();
  $cs->registerScriptFile($baseUrl . '/js/custom.js');

?>

<div class="form">

<?php echo CHtml::beginForm(); ?>
<div>
  <?php echo CHtml::label('Insert your facebook page here (ie: MommyForDummy, or IKEASingapore, or barackobama)', '', array()); ?>
</div>
<div>
  <?php echo CHtml::textField('fb_page_url', '', array('size'=>60,'maxlength'=>128, 'id' => 'fb-page-url')); ?>
</div>
<div>
  <?php
		echo CHtml::ajaxSubmitButton(
			'Get the csv files!',
			array('facebook/generatecsv'),
			array(
				'update'=>'#results',
			)
		);
  ?>
</div>
<?php echo CHtml::endForm(); ?>
</form>
<div id="loading">Loading...</div>
<pre>
<div id="results"></div>
</pre>



