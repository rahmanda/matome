<?php
use Illuminate\Routing\Controller;
use Dingo\Api\Routing\ControllerTrait; 
use Matome\Storage\DocRepository;
use Matome\Metadata\FormSchema;

class MetadatasController extends Controller {
  use ControllerTrait;

  /**
   * Store DocsRepository
   * 
   * @var App\Repositories\DocsRepository
   */
  private $doc;

  /**
   * Constructor
   */
  function __construct(DocRepository $doc, FormSchema $formSchema) {
    $this->doc = $doc;
    $this->formSchema = $formSchema;
    $this->xsdUrl = public_path().'/metadata.xsd';
  }

  /**
   * Show metadata view
   * @return view
   */
  public function index() {
    return View::make('app', array('route' => 'metadata'));
  }

  /**
   * Get all metadata
   * @param  string $orderBy
   * @param  string $order  
   * @return json         
   */
  public function all($orderBy = null, $order = 'desc') {
    $sort = array('orderBy' => $orderBy, 'order' => $order);
    return $this->response->array($this->doc->pagination(25, $sort));
  }

  /**
   * Get complete metadata
   * @param  string $orderBy
   * @param  string $order  
   * @return json         
   */
  public function complete($orderBy = null, $order = 'desc') {
    $sort = array('orderBy' => $orderBy, 'order' => $order);
    return $this->response->array($this->doc->getCompleteMetadatas(25, $sort));
  }

  /**
   * Get incomplete metadata
   * @param  string $orderBy
   * @param  string $order  
   * @return json         
   */
  public function incomplete($orderBy = null, $order = 'desc') {
    $sort = array('orderBy' => $orderBy, 'order' => $order);
    return $this->response->array($this->doc->getUncompleteMetadatas(25, $sort));
  }

  /**
   * Get count metadata
   * @return json
   */
  public function getCount() {
    $complete = $this->doc->completeMetadatasCount();
    $uncomplete = $this->doc->uncompleteMetadatasCount();
    $all = $this->doc->count();

    return $this->response->array(array('complete' => $complete, 'uncomplete' => $uncomplete, 'all' => $all));
  }

  /**
   * Show add view
   * @return view
   */
  public function add() {
    return View::make('add', array('route' => 'addMetadata'));
  }

  /**
   * Store new metadata record
   * @return json
   */
  public function store() {
    $metadata = Input::all();
    $store = $this->doc->storeMetadata($metadata);

    if(!$store) {
      return $this->response->errorBadRequest($this->doc->errors);
    }

    return $this->response->array(Input::all());
  }

  /**
   * Update metadata
   * @param  string $docId
   * @return json       
   */
  public function update($docId) {

    $metadata = Input::all();

    $update = $this->doc->updateMetadata($metadata, $docId);

    if(!$update) {
      return $this->response->errorBadRequest($this->doc->errors);
    }

    return $this->response->array('Successfully update metadata.');

  }

  /**
   * Delete metadata records
   * @return json
   */
  public function destroy() {
    $id = Input::input('id');

    $destroy = $this->doc->destroy($id);

    if(!$destroy) {
      return $this->response->errorBadRequest($this->doc->errors);
    }

    return $this->response->array($destroy);
  }

  /**
   * Get metadata by id
   * @param  string $id
   * @return json    
   */
  public function show($id) {
    return $this->response->array($this->doc->getMetadataById($id));
  }

  /**
   * Get form schema
   * 
   * @param  string $ext extension
   * @return either array or json
   */
  public function getFormSchema($ext) {
    $extension = explode('.', $ext);
    $schema = file_get_contents($this->xsdUrl);
    return $this->formSchema->getFormSchema($schema, $extension[1]);
  }

  public function storeMetaCron() {
    $doc = new Matome\Storage\DocRepository();
    $yesterday = date("Y-m-d");
    $directory = $_ENV['SYNC_DIRECTORY'].'logger/'.$yesterday.'/new/';
    $files = File::files($directory);
    
    // if(!empty($files)) {
    //   foreach($files as $file) {
    //     $xml = (array)simplexml_load_file($file);
    //     $save = $doc->storeMetadata($xml, true); 
    //   }
    // }

    return $files;

    // $directory = storage_path().'/docs/logger/'.$yesterday.'/new/';
    // $files = File::files($directory);
    // if(!empty($files)) {
    //   foreach($files as $file) {
    //     $xml = (array)simplexml_load_file($file);
    //     $save = $doc->updateMetadata($xml, null, true);
    //   }
    // }
  }
}