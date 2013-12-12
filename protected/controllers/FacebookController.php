<?php

class FacebookController extends Controller {

  /** 
   *
   */
  public function actionIndex() {
    $this->render('index');

  }

  /** 
   *
   */
  protected function afterRender($view, &$output) {
    parent::afterRender($view,$output);
    //Yii::app()->facebook->addJsCallback($js); // use this if you are registering any $js code you want to run asyc
    Yii::app()->facebook->initJs($output); // this initializes the Facebook JS SDK on all pages
    Yii::app()->facebook->renderOGMetaTags(); // this renders the OG tags

    return true;
  }

  /** 
   *
   */
  protected function generateFbPostFeed($fbData) {
    $csvArr = array();

    $i = 0;
    foreach($fbData['data'] as $key => $value) {
      foreach($value as $key2 => $value2) {
        if(!is_array($value2)) {
          $csvArr['all_posts'][$i][$key2] = $value2;
        } 
        else {
          if($key2 == 'from') {
            foreach ($value2 as $key3 => $value3) {
              if($key3 == 'category_list') {
                foreach ($value3 as $key4 => $value4) {
                  $csvArr['all_posts'][$i][$key2 . '_' . $key3 . '_' . $key4 . '_id'] = $value4['id'];
                  $csvArr['all_posts'][$i][$key2 . '_' . $key3 . '_' . $key4 . '_name'] = $value4['name'];
                }
              }
              else {
                $csvArr['all_posts'][$i][$key2 . '_' . $key3] = $value3;
              }
            }
          }

          if($key2 == 'actions') {
            foreach ($value2 as $key3 => $value3) {
              $csvArr['all_posts'][$i][$key2 . '_' . $key3 . '_name'] = $value3['name'];
              $csvArr['all_posts'][$i][$key2 . '_' . $key3 . '_link'] = $value3['link'];
            }  
          }

          if($key2 == 'likes') {
            $csvArr['all_likes'][$value['id']] = $value2['data'];
          }
          
        }        
      } 
      $i++;
    }    
    return $csvArr;
  }

  /** 
   *
   */
  protected function generateFbLikesDetail($fbData) {
    $csvArr = array();
    $i = 0;
    foreach ($fbData as $key => $value) {
      foreach ($value as $key2 => $value2) {
        $csvArr[$value2['id']]['post_id'] = $key;
        $csvArr[$value2['id']]['name'] = $value2['name'];
        $csvArr[$value2['id']]['id'] = $value2['id'];
        $i++;
      }
    }
    return $csvArr;
  }

  /** 
   *
   */
  protected function generateCsvFile($tagFileName, $csvArr, $fbPageUrl) {
    $fileName = $tagFileName . '-' . $fbPageUrl . '-' . date("Y-m-d_H-i",time()) . '.csv';
    header('content-type:application/csv;charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');

    $fp = fopen ($_SERVER['DOCUMENT_ROOT'] . '/csv-export/' . $fileName, "w");
    foreach ($csvArr as $fields) {
      fputcsv($fp, $fields);
    }
    fclose($fp);

    return CHtml::link('Download ' . $tagFileName . ' for ' . $fbPageUrl . ' CSV file','/csv-export/' . $fileName);
  }

  /** 
   *
   */
  public function actionGeneratecsv() {
    $fbPageUrl = $_POST['fb_page_url'];

    $fb = Yii::app()->facebook;
    $token = $fb->getAccessToken();
    $fb->setAccessToken($token);
    $fbUser = $fb->getUser();

    $fbPost = NULL;
    $output = NULL;
    
    if ($fbUser) {
      try {
        // Generate Fb Post Feed.
        $fbPost = $fb->api('/' . $fbPageUrl . '/posts?limit=25');
        $fbPostCsvArr = $this->generateFbPostFeed($fbPost);
        $output = $this->generateCsvFile('post-feed', $fbPostCsvArr['all_posts'], $fbPageUrl);

        // Generate Fb Likes Detail.
        $fbLikeCsvArr = $this->generateFbLikesDetail($fbPostCsvArr['all_likes']);
        $output .= '<br />' . $this->generateCsvFile('likes-detail', $fbLikeCsvArr, $fbPageUrl);
        
      } catch (FacebookApiException $e) {
        error_log($e);
        $output = NULL;

      }
    }
    $output .= '<br />' . CHtml::encode(print_r($fbPostCsvArr['all_posts'], true));
    $output .= CHtml::encode(print_r($fbLikeCsvArr, true)); 

    echo $output;
  }


}


