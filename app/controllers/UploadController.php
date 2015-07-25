<?php 
use Illuminate\Routing\Controller;
use Dingo\Api\Routing\ControllerTrait; 

class UploadController extends Controller {
  use ControllerTrait;

  public $errors = array();

  /**
   * Upload document, restrict for pdf type only
   * 
   * @return Response
   */
  public function upload() {

    $files = Input::file('files');
    // $date = Input::get('date');
    // list($year, $month, $day) = explode('-', $date);

    // $destinationPath = storage_path().'/app/docs/'.$year.'/'.$month.'/'; // need to be configurable later
    $destinationPath = public_path().'/files/'; 

    foreach($files as $file) {
      if($this->isValidPdf($file)) {
        $filename = str_random(40).'.pdf';
        $file->move($destinationPath, $filename);
      } else {
        array_push($this->errors, 'File '.$file->getClientOriginalName().' is not valid pdf.');
      }
    }

    if(count($this->errors)) {
      return $this->response->array($this->errors);
    }

    $data = array('filename' => $filename, 'originalFilename' => $file->getClientOriginalName());

    return $this->response->array(array('data' => $data, 'code' => 200));

  }

  /**
   * Validate if file format is pdf or not
   * 
   * @param  array  $input 
   * @param  array  $rules 
   * @return boolean        
   */
  public function isValidPdf($doc) {

    if($doc->getClientMimeType() == 'application/pdf' && $doc->getClientOriginalExtension() == 'pdf') {
      return true;
    }

    return false;

  }
}
